<x-manage.layout>
    <x-slot name="title">游戏编辑</x-slot>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('assets/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css') }}">
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
        <script src="{{ asset('assets/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('assets/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-zh_CN.min.js') }}"></script>
        <script>
            var $channelSelect = $('#channelSelect');
            var $gameSelect = $('#gameSelect');
            $channelSelect.on('change', function () {
                var channel = $(this).val();
                if (!channel) return;

                $.getJSON('{{ url('/admin/game/query') }}', {channel}, function (resp) {
                    if (resp.code > 0) {
                        alert(resp.msg);
                        return;
                    }

                    $gameSelect.html('<option>请选择游戏</option>');
                    $.each(resp.data, function (key, value) {
                        $gameSelect.append(`<option value='${JSON.stringify(value)}'>${value.name}${value.sub_name ? ' - ' + value.sub_name : ''} - ${value.start_at}</option>`);
                    })

                    // 缺一不可
                    $gameSelect.selectpicker('refresh');
                    $gameSelect.selectpicker('render');
                });
            })
        </script>
    </x-slot>
</x-manage.layout>
