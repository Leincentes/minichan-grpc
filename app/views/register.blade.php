@include('header')

<body>
  <div class="wrapper">
    <section class="form signup">
      <header style="text-align: center;">gRPC Register</header>
      <form method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="error-message" style="display: none;"></div>
        <div class="field input">
            <label>Username</label>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Password" required>
          <i class="fas fa-eye"></i>
        </div>

        <div class="field image">
          <label>Select Image</label>
          <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Register">
        </div>
      </form>
      <div class="link">Already signed up? <a href="/login">Login now</a></div>
    </section>
  </div>

  <script src="views/js/pass-show-hide.js"></script>
  <script src="views/js/register.js"></script>

</body>
</html>

<!-- @include('header')
<body>
  <div class="wrapper">
    <section class="form signup">
      <header style="text-align: center;">gRPC Register</header>
      <form method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="error-message" style="display: none;"></div>
        <div class="field input">
            <label>Username</label>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Password" required>
          <i class="fas fa-eye"></i>
        </div>

        <div class="field image">
          <label>Select Image</label>
          <br>
          <div class="image-options" style="display: flex; text-align:center; justify-content:center;">
          </div>
          <div id="selectedIndicator" style="display: none; text-align: center;"></div>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Register">
        </div>
      </form>
      <div class="link">Already signed up? <a href="/login">Login now</a></div>
    </section>
  </div>
  <script src="views/js/pass-show-hide.js"></script>
  <script src="views/js/register.js"></script>
  <script>
  const images = [
    { src: 'views/img/male.jpeg', alt: 'Male Avatar' },
    { src: 'views/img/female.png', alt: 'Female Avatar' }
  ];

  const imageOptionsContainer = document.querySelector('.image-options');
  const selectedIndicator = document.getElementById('selectedIndicator');
  const imageInput = document.querySelector('input[name="image"]');

  images.forEach((image, index) => {
    const label = document.createElement('label');
    const radio = document.createElement('input');
    radio.type = 'radio';
    radio.name = 'avatar';
    radio.value = image.src;
    radio.required = true;
    radio.hidden = true;

    const img = document.createElement('img');
    img.src = image.src;
    img.alt = image.alt;
    img.style.width = '50%';
    img.onclick = () => selectImage(radio, image.alt);

    label.appendChild(radio);
    label.appendChild(img);
    imageOptionsContainer.appendChild(label);
  });

  function selectImage(radio, alt) {
    radio.checked = true;
    selectedIndicator.textContent = `Selected: ${alt}`;
    selectedIndicator.style.display = 'block';
    imageInput.value = radio.value; 
  }
</script>

</body> -->
