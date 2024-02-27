@include('header')

<body>
  <div class="wrapper">
    <section class="form login">
      <header  style="text-align: center;">gRPC Login</header>
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
        <div class="field button">
          <input type="submit" name="submit" value="Login">
        </div>
      </form>
      <div class="link">Not yet signed up? <a href="/register">Sign up now</a></div>
    </section>
  </div>
  
  <script src="views/js/pass-show-hide.js"></script>
  <script src="views/js/login.js"></script>

</body>
