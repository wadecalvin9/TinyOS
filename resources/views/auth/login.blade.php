<x-authlayout>
    <form action="{{ route('auth.login') }}" method="post">

        @csrf
        <div class="details">

            <h1>Login</h1>
            @if (session('error'))
                <p style="font-size: 10px; color: red;">{{ session('error') }}</p>
            @endif
            <div class="inputs">
                <input type="email" name="email" id="" placeholder="email">
            </div>
            <div class="inputs">
                <input type="password" name="password" id="" placeholder="Password">
            </div>
            <div class="st">
                <p>Don't have an acount? </p> <a href="/register">Register</a>
            </div>
            <button><i class="fa-solid fa-arrow-right"></i></button>
        </div>

    </form>

</x-authlayout>
