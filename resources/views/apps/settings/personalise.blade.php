<x-settings>

    <div class="section active" id="personalization">
        <h1>Personalization</h1>
        <p>Background, colors, themes, and fonts.</p>
        <p style="padding: 10px; ">Desktop background</p>

        <form id="backgroundForm" method="POST" action="/backround/change">
            @csrf
            @method('PUT')
            <div class="imagecards">
                <div class="cards">
                     <label>
                        <input type="radio" name="background_url"
                            value="https://images.pexels.com/photos/1365795/pexels-photo-1365795.jpeg">
                        <img src="https://images.pexels.com/photos/1365795/pexels-photo-1365795.jpeg" alt="">
                    </label>
                    <label>
                        <input type="radio" name="background_url"
                            value="https://images.pexels.com/photos/2449605/pexels-photo-2449605.png">
                        <img src="https://images.pexels.com/photos/2449605/pexels-photo-2449605.png" alt="">
                    </label>
                    <label>
                        <input type="radio" name="background_url"
                            value="https://images.pexels.com/photos/5477427/pexels-photo-5477427.jpeg">
                        <img src="https://images.pexels.com/photos/5477427/pexels-photo-5477427.jpeg" alt="">
                    </label>
                    <label>
                        <input type="radio" name="background_url"
                            value="https://images.pexels.com/photos/34190300/pexels-photo-34190300.jpeg">
                        <img src="https://images.pexels.com/photos/34190300/pexels-photo-34190300.jpeg" alt="">
                    </label>





                </div>
            </div>
            <button class="sbt" type="submit">Apply Background</button>
        </form>
    </div>

    <script>
document.querySelectorAll('input[name="background_url"]').forEach(radio => {
  radio.addEventListener('change', function() {
    document.body.style.backgroundImage = `url('${this.value}')`;
    document.body.style.backgroundSize = 'cover';
    document.body.style.backgroundPosition = 'center';
  });
});
</script>


</x-settings>
