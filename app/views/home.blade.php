@include('header')

<body>
  <div class="wrapper">
    <section class="users">
        <header>
            <div class="content">
                <img src="/views/img/{{ $image }}" alt="profile.png">
                <div class="details">
                    <span>{{ $username }}</span>
                    <p>{{ $status }} </p>
                </div>
            </div>
            <a href="/logout" class="logout">Logout</a>
        </header>
        <div class="search">
          <span class="text">Select an user to start chat</span>
          <input type="text" placeholder="Enter name to search...">
          <button><i class="fas fa-search"></i></button>
        </div>
        
        <div class="users-list">
        </div>
    </section>
  </div>  
  <script src="views/js/users.js"></script>
</body>
</html>
