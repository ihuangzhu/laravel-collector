<x-manage.layout>
    <x-slot name="title">登录</x-slot>

    <div class="modal position-static d-block py-5" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-5 shadow">
                <div class="modal-header p-5 pb-4 border-bottom-0">
                    <h2 class="fw-bold mb-0">登录</h2>
                </div>

                <div class="modal-body p-5 pt-0">
                    <form action="{{ url()->current() }}" method="post">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control rounded-4" id="emailInput"
                                   placeholder="name@example.com">
                            <label for="emailInput">邮箱地址</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control rounded-4" id="passwordPassword"
                                   placeholder="***">
                            <label for="passwordPassword">密码</label>
                        </div>
                        <button class="w-100 mb-2 btn btn-lg rounded-4 btn-primary" type="submit">登录</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-manage.layout>