
    document.getElementById("signupForm").addEventListener("submit", function(e) {
        e.preventDefault();

        let form = new FormData(this);
        let msg = document.getElementById("msg");
        msg.innerHTML = "Processing, please wait...";

        fetch("auth/signup.php", {
                method: "POST",
                body: form
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    msg.innerHTML = "Check your email to verify your account";
                } else {
                    msg.innerHTML = data;
                }
            });
    });

