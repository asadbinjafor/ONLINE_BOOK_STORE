
function validateRegistration()
{
    var name = document.getElementById("reg_name").value.trim();
    var email = document.getElementById("reg_email").value.trim();
    var password = document.getElementById("reg_password").value;
    var address = document.getElementById("address").value.trim();
    var phone = document.getElementById("phone").value.trim();

    if (name === "") { alert("Full name is required"); return false; }
    if (email === "") { alert("Email is required"); return false; }
    if (password === "") { alert("Password is required"); return false; }
    if (address === "") { alert("Address is required"); return false; }
    if (phone === "") { alert("Phone number is required"); return false; }
    if (!/^[0-9]+$/.test(phone)) { alert("Phone number must contain digits only"); return false; }
    if (password.length < 8) { alert("Password must be at least 8 characters"); return false; }
    if (email.indexOf("@") === -1 || email.indexOf(".") === -1) { alert("Enter a valid email address"); return false; }
    return true;
}

function validateProfile()
{
    var name = document.getElementById("name").value.trim();
    var email = document.getElementById("email").value.trim();
    var address = document.getElementById("address").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var currentPassword = document.getElementById("current_password").value;
    var newPassword = document.getElementById("new_password").value;

    if (name === "" || email === "" || address === "" || phone === "") {
        alert("Name, email, address and phone are required");
        return false;
    }
    if (newPassword !== "" && currentPassword === "") {
        alert("Current password is required to change password");
        return false;
    }
    if (newPassword !== "" && newPassword.length < 8) {
        alert("New password must be at least 8 characters");
        return false;
    }
    return true;
}

function searchBooks()
{
    var list = document.getElementById("bookList");
    if (!list) {
        return;
    }

    var q = document.getElementById("searchText").value.trim();
    var author = document.getElementById("authorFilter").value;
    var genre = document.getElementById("genreFilter").value;
    var filter = document.getElementById("filterType").value;

    if (q.length > 100) {
        alert("Search text is too long");
        return;
    }

    var url = "../control/books_search_api.php?q=" + encodeURIComponent(q) +
        "&author=" + encodeURIComponent(author) +
        "&genre=" + encodeURIComponent(genre) +
        "&filter=" + encodeURIComponent(filter);

    list.innerHTML = "<div class='no-results'>Searching...</div>";

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4) {
            if (this.status === 200) {
                var data = JSON.parse(this.responseText);
                if (data.success) {
                    renderBookCards(data.books);
                } else {
                    list.innerHTML = "<div class='no-results'>Search failed.</div>";
                }
            } else {
                list.innerHTML = "<div class='no-results'>Server error.</div>";
            }
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

function escapeHtml(text)
{
    var div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function renderBookCards(books)
{
    var list = document.getElementById("bookList");
    if (!list) {
        return;
    }

    if (!books || books.length === 0) {
        list.innerHTML = "<div class='no-results'>No books found.</div>";
        return;
    }

    var html = "";
    for (var i = 0; i < books.length; i++) {
        var b = books[i];
        html += "<div class='book-card' data-book-id='" + b.id + "'>";
        if (b.image_url) {
            html += "<img class='book-thumb' src='" + escapeHtml(b.image_url) + "' alt=''>";
        }
        html += "<h3><a href='book_detail.php?id=" + b.id + "'>" + escapeHtml(b.title) + "</a></h3>";
        html += "<p>Author: " + escapeHtml(b.author) + "</p>";
        html += "<p>Genre: " + escapeHtml(b.category_name || "") + "</p>";
        html += "<p>Stock: " + b.stock + " units</p>";
        html += "<p class='book-price'>BDT " + b.price + "</p>";
        html += "<a class='btn-link' href='book_detail.php?id=" + b.id + "'>View Details</a>";
        html += "</div>";
    }
    list.innerHTML = html;

    if (typeof addCartButtonsToBookCards === "function") {
        addCartButtonsToBookCards(books);
    }
}
