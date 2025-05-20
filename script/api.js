function element(id) {
	return document.getElementById(id);
}

function hide(id) {
	element(id).style.display = "none";
}

function show(id) {
	element(id).style.display = "block";
}

function message(id, text) {
	element(id).innerHTML = text;
}

async function sendRequest(data) {
	const response = await fetch('api.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(data)
	});
	const json = await response.json();
	return json;
}



function ajax(data, callback) {
	var req = new XMLHttpRequest();
	req.onreadystatechange = function () {
		if (req.readyState == 4 && req.status == 200) {
			var json = JSON.parse(req.responseText);
			callback(json);
		}
	};

	req.open("POST", "api.php", true);

	// This is important for POST requests with data that is not submitted via a form.
	req.setRequestHeader("Content-type", "application/json");
	req.send(JSON.stringify(data));
}

function login() {
	if (isValidEmail(element("email").value) && isValidPassword(element("password").value)) {
		sendRequest({
			"type": "LOGIN",
			"email": element("email").value,
			"password": element("password").value
		}).then((data) => {
			if (data.status == "success") {
				sessionStorage.setItem("sessionID", data.Data.apikey);
				sessionStorage.setItem("userID", data.Data.id);
				alert("User has been logged in successfully");
			} else {
				alert("Invalid credentials");
			}
		})
	}
}

function signup() {
	let email = element("email").value;
	let password = element("password").value;

	if (isValidEmail(email) && isValidPassword(password) && isValidInput(element("name").value) && isValidInput(element("username").value) && isValidInput(element("surname").value)) {
		sendRequest({
			"type": "REGISTER",
			"username": element("username").value,
			"name": element("name").value,
			"email": element("email").value,
			"surname": element("surname").value,
			"password": element("password").value
		}).then((data) => {
			if (data.status == "success") {
				sessionStorage.setItem("sessionID", data.Data.apikey);
				sessionStorage.setItem("userID", data.Data.id);
				alert("User has been registered successfully");
			} else {
				alert(data.status);
			}
		})


	}
}

function save() {
	sendRequest({
		"type": "SAVE",
		"key": sessionStorage.getItem("sessionID"),
		"brand": element("brand").value,
		"currency": element("currency").value,
		"manufacturer": element("manufacturer").value,
		"department": element("department").value
	}).then((data) => {
		if (data.status == "success") {
			sessionStorage.setItem("sessionID", data.Data.apikey);
			alert("Preferences saved");
		} else {
			alert(data.status);
		}
	})


}

function addwishlist() {
	sendRequest({
		"type": "WISHLIST",
		"action": "add",
		"key": sessionStorage.getItem("sessionID"),
		"id": sessionStorage.getItem("userID"),
		"product": element("product").value
	}).then((data) => {
		if (data.status == "success") {
			sessionStorage.setItem("sessionID", data.Data.apikey);
			alert("Product added to wishlist");
		} else {
			alert(data.status);
		}
	})

}

function removewishlist() {
	sendRequest({
		"type": "WISHLIST",
		"action": "remove",
		"key": sessionStorage.getItem("sessionID"),
		"id": sessionStorage.getItem("userID"),
		"product": element("product").value
	}).then((data) => {
		if (data.status == "success") {
			sessionStorage.setItem("sessionID", data.Data.apikey);
			alert("Product removed from wishlist");
		} else {
			alert(data.status);
		}
	})

}

function addcart() {
	sendRequest({
		"type": "CART",
		"action": "add",
		"key": sessionStorage.getItem("sessionID"),
		"id": sessionStorage.getItem("userID"),
		"product": element("product").value
	}).then((data) => {
		if (data.status == "success") {
			sessionStorage.setItem("sessionID", data.Data.apikey);
			alert("Product added to cart");
		} else {
			alert(data.status);
		}
	})

}

function removecart() {
	sendRequest({
		"type": "CART",
		"action": "remove",
		"key": sessionStorage.getItem("sessionID"),
		"id": sessionStorage.getItem("userID"),
		"product": element("product").value
	}).then((data) => {
		if (data.status == "success") {
			sessionStorage.setItem("sessionID", data.Data.apikey);
			alert("Product removed from cart");
		} else {
			alert(data.status);
		}
	})

}



function setCookie(name, value) {
	document.cookie = name + "=" + value;
}

function getCookie(name) {
	let cname = name + "=";
	let cookie = document.cookie;
	while (cookie.charAt(0) == ' ') {
		cookie = cookie.substring(1);
	}
	if (cookie.indexOf(name) == 0) {
		return cookie.substring(name.length, cookie.length);
	}

	return "";

}

function checkCookie() {
	let sessionID = getCookie("sessionID");
}

function isValidPassword(password) {
	let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&])[A-Za-z\d@.#$!%*?&]{8,50}$/;
	if (regex.test(password) == false) {
		message("message", "Password must contain uppercase and lowercase letters, at least one number and one special character, and be between 8 and 50 characters long");
	}
	return regex.test(password);
}

function isValidEmail(email) {
	if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) == false) {
		message("message", "Invalid email field input");
	}

	return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidInput(input) {
	let regex = /^[a-zA-Z]{1,50}$/;
	if (regex.test(input) == false) {
		message("message", "All input fields are required");
	}

	return regex.test(input);
}

function logout() {
	sessionStorage.clear();
	location.reload();
}

//ajax({
//		"name": element("name").value,
//	"surname": element("surname").value,
//"email": element("email").value,
//"type": "Customer",
//"password": element("password").value
//}), function (data) {
//if (data.success) {
//hide("login");
//show("logout");
//message("music", "Loading ...");
//alert(data.data.key); // store this key in DOM storage and use it for other API calls
// for visual purposes I have just doing an ajax call on info with the parameters below.
// This is to give you the starting point
//ajax({
//		"key": data.data.key,
//	"type": "info",
//	"title": "Iron+Man",
//"return": ["title", "rank"]
//	}, function (data) {
//	message("music", JSON.stringify(data, undefined, 4)); // now fill this info in your template from P2
//})

//}
//}
