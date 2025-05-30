<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include 'userHeader.php';?>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <div class="hero-title">
                <h1 class="gradient-text">Connecting Your<br>Destinations One Flight<br>at a Time.</h1>
            </div>
        </div>
        
        <div class="flight-search">
            <div class="search-input">
                <div class="input-group">
                    <img src="icon/from-destination.svg" alt="" class="icon">
                    <input type="text" placeholder="From where?">
                    <span class="input-border"></span>
                </div>
            </div>
            <div class="search-input">
                <div class="input-group">
                    <img src="icon/destination.svg" alt="" class="icon">
                    <input type="text" placeholder="Where to?">
                    <span class="input-border"></span>
                </div>
            </div>
            <div class="search-input">
                <div class="input-group">
                    <img src="icon/calendar.svg" alt="" class="icon">
                    <input type="text" placeholder="Depart - Return">
                    <span class="input-border"></span>
                </div>
            </div>
            <div class="search-input">
                <div class="input-group">
                    <img src="icon/person.svg" alt="" class="icon">
                    <input type="text" placeholder="1 adult">
                    <span class="input-border"></span>
                </div>
            </div>
            <button class="btn search-btn">Search</button>
        </div>
    </section>
    
    <section class="flight-recommendations">
        <div class="card-header">
            <h3 class="section-title">Find your next adventure with these <span class="highlight">flight recommendations!</span></h3>
            <a href="#" class="see-all">
                All
                <img src="icon/arrow-right.svg" alt="See all" class="icon">
            </a>
        </div>
        
        <div class="card-container">
            <div class="card-row">
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/kuala-lumpur.png" alt="Kuala Lumpur">
                            <div class="card-overlay">
                                <h3>Kuala Lumpur</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-utensils"></i>
                                        <span>Variety of Foods</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-city"></i>
                                        <span>Urban Adventure</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-building"></i>
                                        <span>Petronas Towers</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">Kuala Lumpur, <span class="highlight">Malaysia</span></h4>
                                <span class="card-price">RM 300</span>
                            </div>
                            <p class="card-description">Home to the iconic Petronas Towers and a vibrant city center.</p>
                        </div>
                    </div>
                </a>
                
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/marina-bay.png" alt="Marina Bay">
                            <div class="card-overlay">
                                <h3>Singapore</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-leaf"></i>
                                        <span>Gardens by the Bay</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-utensils"></i>
                                        <span>Food Paradise</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>Shopping Haven</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">Marina Bay, <span class="highlight">Singapore</span></h4>
                                <span class="card-price">RM 400</span>
                            </div>
                            <p class="card-description">Singapore's iconic waterfront hub of attractions and luxury.</p>
                        </div>
                    </div>
                </a>
                
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/langkawi.png" alt="Langkawi">
                            <div class="card-overlay">
                                <h3>Langkawi</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-umbrella-beach"></i>
                                        <span>Pristine Beaches</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-water"></i>
                                        <span>Crystal Waters</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-mountain"></i>
                                        <span>Nature Excursions</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">Langkawi, <span class="highlight">Malaysia</span></h4>
                                <span class="card-price">RM 200</span>
                            </div>
                            <p class="card-description">An island paradise of beaches and natural wonders.</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="card-row">
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/fujiyoshida.png" alt="Fujiyoshida">
                            <div class="card-overlay">
                                <h3>Mount Fuji</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-mountain"></i>
                                        <span>Iconic Mountain</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-tree"></i>
                                        <span>Scenic Hiking</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-spa"></i>
                                        <span>Hot Springs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">Fujiyoshida, <span class="highlight">Japan</span></h4>
                                <span class="card-price">RM 1,000</span>
                            </div>
                            <p class="card-description">A charming city at the base of Mount Fuji, known for its rich cultural heritage, iconic Chureito Pagoda, and stunning views of Japan's most famous mountain.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    
    <section class="unique-stays">
        <div class="card-header">
            <h3 class="section-title">Explore unique places to <span class="highlight">stay!</span></h3>
            <a href="#" class="see-all">
                All
                <img src="icon/arrow-right.svg" alt="See all" class="icon">
            </a>
        </div>
        
        <div class="card-container">
            <div class="card-row">
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/pavilion.webp" alt="Pavilion Hotel Kuala Lumpur">
                            <div class="card-overlay">
                                <h3>Pavilion Hotel KL</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>Shopping District</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-star"></i>
                                        <span>5-Star Luxury</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-city"></i>
                                        <span>City Views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">Pavilion Hotel <span class="highlight">Kuala Lumpur</span></h4>
                            </div>
                            <p class="card-description">Stay in the heart of Kuala Lumpur, just steps away from the famous Pavilion shopping mall. This 5-star hotel offers a perfect blend of luxury and modern design, with panoramic city views and world-class service.</p>
                        </div>
                    </div>
                </a>
                
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/ghotel.jpg" alt="The G Hotel Gurney, Penang">
                            <div class="card-overlay">
                                <h3>The G Hotel Gurney</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-water"></i>
                                        <span>Seaside Location</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-utensils"></i>
                                        <span>Culinary Delights</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-concierge-bell"></i>
                                        <span>5-Star Service</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">The G Hotel Gurney, <span class="highlight">Penang</span></h4>
                            </div>
                            <p class="card-description">Experience luxury by the sea in Penang at The G Hotel Gurney, located along the scenic Gurney Drive. Known for its chic and contemporary design, this 5-star hotel offers impeccable service and easy access to Penang's top attractions.</p>
                        </div>
                    </div>
                </a>
                
                <a href=# class="card-link">
                    <div class="card">
                        <div class="card-image">
                            <img src="background/berjaya.jpeg" alt="Berjaya Langkawi Resort">
                            <div class="card-overlay">
                                <h3>Berjaya Langkawi</h3>
                                <div class="overlay-icons">
                                    <div class="icon-item">
                                        <i class="fas fa-tree"></i>
                                        <span>Rainforest Setting</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-umbrella-beach"></i>
                                        <span>Beach Access</span>
                                    </div>
                                    <div class="icon-item">
                                        <i class="fas fa-spa"></i>
                                        <span>Luxury Amenities</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <h4 class="card-title">Berjaya <span class="highlight">Langkawi</span> Resort</h4>
                            </div>
                            <p class="card-description">Escape to paradise at Berjaya Langkawi Resort, nestled in a lush tropical rainforest by the crystal-clear waters of Langkawi. Offering beachfront chalets and luxurious suites perfect for relaxing and unwinding in nature.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="testimonial-header">
            <h3 class="section-title">What <span class="highlight">Travooli</span> users are saying</h3>
        </div>
        
        <div class="testimonial-container">
            <!-- Testimonial 1 -->
            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="background/user-avatar-1.png" alt="Ahmad Zulkifli" class="user-avatar">
                    <div class="testimonial-content">
                        <div class="user-info">
                            <h3 class="user-name">Ahmad Zulkifli</h3>
                            <p class="user-location">Kuala Lumpur, Malaysia | March 2025</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">
                            What a fantastic experience using Travooli! I booked all of my flights for my family trip to Langkawi through Travooli and everything was perfect. When I needed to adjust my accommodation dates, Travooli support was incredibly helpful and sorted it out immediately.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="background/user-avatar-2.png" alt="Lim Wei Ling" class="user-avatar">
                    <div class="testimonial-content">
                        <div class="user-info">
                            <h3 class="user-name">Lim Wei Ling</h3>
                            <p class="user-location">Singapore | January 2025</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">
                            My friends and I travel to Malaysia twice a year, and we've always used other booking platforms. Travooli was recommended by a colleague, and I'm so glad we tried it! The process was seamless and the prices were much better than what we typically find elsewhere.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="testimonial-card">
                <div class="testimonial-user">
                    <img src="background/user-avatar-3.png" alt="Amirah Hassan" class="user-avatar">
                    <div class="testimonial-content">
                        <div class="user-info">
                            <h3 class="user-name">Amirah Hassan</h3>
                            <p class="user-location">Penang, Malaysia | April 2025</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">
                            When I needed to book flights from Penang to Kota Kinabalu, Travooli had the best user experience so I decided to give it a try. The interface was clean and intuitive, and I found amazing deals that weren't available on other sites. I'll definitely be using Travooli for all my future travels.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script src="js/script.js"></script>

    <?php include 'u_footer_1.php'; ?>
    <?php include 'u_footer_2.php'; ?>
</body>
</html>
