<x-authlayout>
    <form action="{{ route('auth.register') }}" method="post">
        @csrf
        <div class="details">
            <h1>Create User</h1>
            <div class="inputs">
                <input type="text" name="name" id="" placeholder="Username">
            </div>
            <div class="inputs">
                <input type="email" name="email" id="" placeholder="email">
            </div>
            <div class="inputs">
                <input type="Password" name="password" id="" placeholder="Password">
            </div>
            <div class="st">
                <p>already have an acount? </p> <a href="/login">login</a>
            </div>
            <button><i class="fa-solid fa-arrow-right"></i></button>
        </div>

    </form>


</x-authlayout>
