
let isSearching = false;

const searchBar = document.querySelector(".search input"),
      searchIcon = document.querySelector(".search button"),
      usersList = document.querySelector(".users-list");


searchIcon.onclick = ()=>{
  isSearching = true;

  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
  searchBar.onblur = () => {
    isSearching = false;
  };
}

searchBar.onkeyup = () => {
  let searchTerm = searchBar.value.trim(); 
  let url = `/search?searchTerm=${searchTerm}`;
  fetch(url, {
    method: "GET"
  })
  .then(response => {
    if (response.ok) {
      return response.json();
    } else {
      throw new Error("Request failed with status " + response.status);
    }
  })
  .then(data => {
    if (data && data.length > 0) { 
      const userListHTML = data.map(user => { 
        const { uniqueId, username, image, status } = user;
        const offline = status !== 'active' ? 'offline' : '';
        return `
          <a href="/chat?user_id=${uniqueId}">
            <div class="content">
              <img src="views/img/${image}" alt="">
              <div class="details">
                <span>${username}</span>
              </div>
            </div>
            <div class="status-dot ${offline}"><i class="fas fa-circle"></i></div>
          </a>
        `;
      }).join('');
      usersList.innerHTML = userListHTML; 
    } else {
      usersList.innerHTML = 'No users found';
    }
  })
  .catch(error => {
    console.error(error);
    usersList.innerHTML = 'An error occurred while fetching users';
  });
};

// Getting Users
document.addEventListener("DOMContentLoaded", function() {
  setInterval(() => {
      if (!isSearching) { 
      const fetchPromise = fetch("/users", {
        method: "GET"
      });

      fetchPromise.then(response => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error("Request failed with status " + response.status);
        }
      })
      .then(data => {
        if (data && Object.keys(data).length > 0) {
          const userListHTML = Object.entries(data).map(([key, value]) => {
            console.log(value);
            const { uniqueId, username, image, status} = value;
              const offline = status !== 'active' ? 'offline' : '';

                return `
                <a href="/chat?user_id=${uniqueId}">
                    <div class="content">
                        <img src="views/img/${image}" alt="">
                        <div class="details">
                            <span>${username}</span>
                        </div>
                    </div>
                    <div class="status-dot ${offline}"><i class="fas fa-circle"></i></div>
                </a>
              `;
          }).join('');
          usersList.innerHTML = userListHTML;
        } else {
            usersList.innerHTML = 'No users are available to chat';
            console.error('Empty response received');
        }
      })
      .catch(error => {
        console.error(error);
      });
    }
    }, 800);
});
