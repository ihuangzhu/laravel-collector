<?php


namespace App\Services;


use App\Enums\GameStatus;
use App\Helpers\CstHelper;
use App\Models\Game;
use App\Models\GameMatch;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;

class CollectCstService
{

    /**
     * @var array
     */
    private $ext;

    /**
     * 更新比赛
     *
     * @param Game $game
     * @return bool
     * @throws GuzzleException
     */
    public function refresh(Game $game)
    {
        if (!$game->rule->exists) return false;
        if ($mapping = json_decode($game->rule->mapping, true) ?: []) {
            if ($mapping['ext'] ?? false) {
                $this->collectExt($mapping['ext']);
            }

            if ($mapping['matches'] ?? false) {
                $this->collectMatch($game, $mapping['matches']);
            }

            return true;
        }

        return false;
    }

    /**
     * 采集其它
     *
     * @param array $ext
     * @throws GuzzleException
     */
    public function collectExt(array $ext)
    {
        foreach ($ext as $key => $value) {
            if (empty($value['method'])) continue;
            if (empty($value['action'])) continue;

            if (strtolower($value['method']) == 'get') {
                $this->ext[$key] = CstHelper::get($value['action']);
            } elseif (strtolower($value['method']) == 'get') {
                $this->ext[$key] = CstHelper::post($value['action']);
            }
        }
    }

    /**
     * 采集比赛
     *
     * @param Game $game
     * @param array $rule
     * @return bool
     * @throws GuzzleException
     */
    public function collectMatch(Game $game, array $rule)
    {
        if (empty($rule['url'])) return false;
        if ($result = CstHelper::get($rule['url']['action'], $rule['url']['data'] ?? [])) {
            list($list, $mapping) = $this->matchList($result, $rule['mappings'] ?? []);
            foreach ($list as $value) {
                if ($this->match($mapping, $value, $matches) === false) continue;

                $scoreA = $matches['score_a'] ?? 0;
                $scoreB = $matches['score_b'] ?? 0;
                if ($gameMatch = GameMatch::query()->where(['game_id' => $game->id, 'sign' => $matches['sign']])->first()) { // 编辑
                    if ($gameMatch->status == GameStatus::END) continue;

                    $gameMatch->score = "{$scoreA}:{$scoreB}";
                    $gameMatch->score_a = $scoreA;
                    $gameMatch->score_b = $scoreB;
                    $gameMatch->status = $matches['status'];
                    $gameMatch->start_at = $matches['start_at'];
                    $gameMatch->update();
                } else { // 新增
                    $teamA = $matches['team_a'] ?? 'A';
                    $teamB = $matches['team_b'] ?? 'B';

                    $gameMatch = new GameMatch();
                    $gameMatch->fill([
                        'game_id' => $game->id,
                        'sign' => $matches['sign'],
                        'name' => "{$teamA} vs {$teamB}",
                        'score' => "{$scoreA}:{$scoreB}",
                        'status' => $matches['status'],
                        'team_a' => $teamA,
                        'team_b' => $teamB,
                        'score_a' => $scoreA,
                        'score_b' => $scoreB,
                        'start_at' => $matches['start_at'],
                    ])->save();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * 匹配数据
     *
     * @param array $mappings
     * @param array $subjects
     * @param array $matches
     * @return bool
     */
    protected function match(array $mappings, array $subjects, &$matches = null)
    {
        foreach ($mappings as $mapping) {
            switch ($mapping['type']) {
                case 'array':
                    foreach ($subjects[$mapping['key']] ?? [] as $value) {
                        $this->match($mapping['children'], $value, $matches);
                    }
                    break;
                case 'object':
                    $childKey = $mapping['child']['key'];
                    $childMapping = $mapping['child']['mapping'];
                    $matches[$childMapping] = $subjects[$mapping['key']][$childKey];
                    break;
                case 'timestamp':
                    $matches[$mapping['mapping']] = Carbon::createFromTimestamp(substr($subjects[$mapping['key']], 0, 10))->format('Y-m-d H:i:s');
                    break;
                default:
                    $matches[$mapping['mapping']] = $subjects[$mapping['key']];
                    break;
            }
        }

        return !empty($matches);
    }

    /**
     * 匹配列表数据
     *
     * @param array $subjects
     * @param array $mappings
     * @return array|mixed
     */
    protected function matchList(array $subjects, array $mappings)
    {
        foreach ($mappings as $mapping) {
            if ($mapping['type'] == 'array') {
                if ($target = $mapping['target'] ?? false) {
                    return [
                        $subjects[$mapping['key']] ?? [],
                        $mapping['children'] ?? [],
                    ];
                }

                foreach ($subjects[$mapping['key']] ?? [] as $value) {
                    return $this->matchList($value, $mapping['children'] ?? []);
                }
            }
        }

        return [];
    }

}
