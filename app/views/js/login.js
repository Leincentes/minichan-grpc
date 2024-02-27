document.addEventListener("DOMContentLoaded", function() {
  const form = document.querySelector(".login form");

  form.addEventListener("submit", function(event) {
      event.preventDefault();

      const formData = new FormData(form);

      const fetchPromise = fetch("/login/user", {
          method: "POST",
          body: formData
      });

      Promise.race([fetchPromise])
      .then(response => {
            console.log(response);
              if (response.ok) {
                  window.location.href = "/home";
              } else if (response.status === 404) { 
                  return response.text().then(error => {
                      throw new Error("User not found"); 
                  });
              } else {
                  return response.text().then(error => {
                      throw new Error(error);
                  });
              }
          })
          .catch(error => {
              const errorText = document.querySelector(".error-text");
              errorText.textContent = error.message;
              errorText.style.display = "block";
          });
  });
});
