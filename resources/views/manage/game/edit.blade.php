<x-manage.layout>
    <x-slot name="title">游戏编辑</x-slot>
    <x-slot name="styles">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
    </x-slot>

    <div class="container">
        <form action="{{ url()->current() }}" method="post">
            @csrf

            <div class="form-group">
                <label for="channelSelect">频道</label>
                <select name="channel" class="form-control selectpicker" id="channelSelect">
                    <option>请选择频道</option>
                    @foreach(\App\Enums\Channel::getMap() as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="gameSelect">游戏</label>
                <select name="game" class="form-control selectpicker" id="gameSelect" data-live-search="true">
                    <option>请先选择频道</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">提交</button>
        </form>
    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js" integrity="sha256-qo0Cam4XJ0QQ06XnCiCFYBh3GDXU45j3lpUp+em2yBU=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/i18n/defaults-zh_CN.min.js" integrity="sha256-zHB2UnROJx8/VorUnkT+nsUKnTsZ7HemVEqeMyOZp/Q=" crossorigin="anonymous"></script>
        <script>
            var $channelSelect = $('#channelSelect');
            var $gameSelect = $('#gameSelect');
            $channelSelect.on('change', function () {
                var channel = $(this).val();
                if (!channel) return;

                $.getJSON('{{ url('/manage/game/query') }}', {channel}, function (resp) {
                    if (resp.code > 0) {
                        alert(resp.msg);
                        return;
                    }

                    $gameSelect.html('<option>请选择游戏</option>');
                    $.each(resp.data, function (key, value) {
                        $gameSelect.append(`<option value='${JSON.stringify(value)}'>${value.name}${value.sub_name ? ' - ' + value.sub_name : ''}</option>`);
                    })

                    // 缺一不可
                    $gameSelect.selectpicker('refresh');
                    $gameSelect.selectpicker('render');
                });
            })
        </script>
    </x-slot>
</x-manage.layout>
