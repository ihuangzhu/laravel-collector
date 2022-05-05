<x-manage.layout>
    <x-slot name="title">游戏列表</x-slot>

    <div class="container">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">频道</th>
                <th scope="col">名称</th>
                <th scope="col">子名称</th>
                <th scope="col">开始时间</th>
                <th scope="col">结束时间</th>
                <th scope="col">状态</th>
                <th scope="col">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($games as $game)
                <tr>
                    <td>{{ $game->id }}</td>
                    <td>{{ $game->channel->name }}</td>
                    <td>{{ $game->name }}</td>
                    <td>{{ $game->sub_name }}</td>
                    <td>{{ $game->start_at }}</td>
                    <td>{{ $game->end_at }}</td>
                    <td>{{ \App\Enums\GameStatus::getMap($game->status) }}</td>
                    <td>
                        <a href="{{ url('/manage/game/edit', ['id' => $game->id]) }}">编辑</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</x-manage.layout>