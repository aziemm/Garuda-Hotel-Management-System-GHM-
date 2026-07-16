<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

// Retrieve the guest ID from the session
$guest_id = $_SESSION['guest_id'] ?? '';

// Logout user if logout button is clicked
if (isset($_POST['logout'])) {
    session_destroy();
    echo "<script>
                alert('Logout successful.');
                window.location.href = 'index.php';
              </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GHM Hotel</title>
    <link rel="stylesheet" href="style/stylehome2.css">
    <link rel="stylesheet" href="style/stylehome.css">
    <style>
        .main-content {
            background-size: cover;
            background-position: center;
            color: white;
            padding: 32px;
            min-height: 100vh;
        }
        h1, p {
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.8);
        }
        .w3-bar {
            display: flex;
            justify-content: space-between;
        }
        .left-nav, .right-nav {
            display: flex;
        }
        .slideshow-container {
            position: relative;
            max-width: 60%;
            margin: 40px auto;
            overflow: hidden;
            border: 2px solid #fff;
        }
        .mySlides {
            display: none;
            transition: opacity 0.5s ease;
            width: 100%;
        }
        .mySlides img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }
        .text {
            color: #f2f2f2;
            font-size: 20px;
            padding: 8px 12px;
            position: absolute;
            bottom: 8px;
            width: 100%;
            text-align: center;
        }
        .numbertext {
            color: #f2f2f2;
            font-size: 14px;
            padding: 8px 12px;
            position: absolute;
            top: 0;
        }
        .dot {
            cursor: pointer;
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }
        .active, .dot:hover {
            background-color: #717171;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="w3-bar w3-transparent">
            <div class="left-nav">
                <a href="booking1.php?guest_id=<?php echo $guest_id; ?>" class="w3-bar-item w3-button">Booking Now</a>
                <a href="history.php?guest_id=<?php echo $guest_id; ?>" class="w3-bar-item w3-button">My Booking</a>
                <a href="check.php?guest_id=<?php echo $guest_id; ?>" class="w3-bar-item w3-button">Check Availability</a>
            </div>
            <div class="right-nav">
                <a href="guest/edit_profile.php?guest_id=<?php echo $guest_id; ?>" class="w3-bar-item w3-button">Edit Profile</a>
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" class="w3-bar-item w3-button">Logout</button>
                </form>
            </div>
        </div>

        <div class="w3-container w3-padding-32 main-content">
            <h1 class="w3-center">Welcome to GHM Hotel</h1>
            <p class="w3-center">Experience luxury and comfort at our hotel. We provide the best services to make your stay memorable.</p>

            <div class="slideshow-container">
                <div class="mySlides">
                    <div class="numbertext">1 / 3</div>
                    <img src="image/bg1.jpg" alt="Branch1">
                    <div class="text">Kota Bahru, Kelantan</div>
                </div>

                <div class="mySlides">
                    <div class="numbertext">2 / 3</div>
                    <img src="image/bg2.jpg" alt="Branch2">
                    <div class="text">Shah Alam, Selangor</div>
                </div>

                <div class="mySlides">
                    <div class="numbertext">3 / 3</div>
                    <img src="image/bg3.jpg" alt="Branch3">
                    <div class="text">Coming Soon</div>
                </div>

                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
            </div>
            <br>

            <div style="text-align:center">
                <span class="dot" onclick="currentSlide(1)"></span> 
                <span class="dot" onclick="currentSlide(2)"></span> 
                <span class="dot" onclick="currentSlide(3)"></span> 
            </div>
        </div>
    </div>

    <footer class="w3-container w3-black w3-center w3-padding-16">
        <h2 class="w3-center">ABOUT GHM</h2>
        <p class="w3-center">Established in 1992, GHM (General Hotel Management Ltd.) is a global hotel management company known for conceptualising, developing and operating an exclusive group of luxury hotels and resorts. With an expansive portfolio in the Middle East and Europe and more projects in the pipeline, GHM prides itself on providing a warm, guest-centred experience – ensuring every stay is memorable for all the right reasons.</p>
        <p class="w3-center">As a global luxury hotel management group, you can trust that GHM’s luxury hotels and resorts are designed for visitors to create magical experiences. Featuring elegant and contemporary designs interpreted with respect for the indigenous culture, rich history and unique surroundings, our luxury hotels can bring out the best of a city.</p>
        <p>&copy; GHM hotel. All Rights Reserved.</p>
    </footer>

    <script>
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}    
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";  
            dots[slideIndex-1].className += " active";
            setTimeout(showSlides, 4000);
        }

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }
    </script>
</body>
</html>
