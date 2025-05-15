<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <?php include 'userHeader.php';?>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <div class="hero-title">
                <img src="background/Hero Text Gradient.png" alt="Connecting Your Destinations One Flight at a Time." class="hero-text-image">
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
    
    <section class="card-section">
        <div class="card-header">
            <h3 class="section-title">Find your next adventure with these <span class="highlight">flight recommendations!</span></h3>
            <a href="#" class="see-all">
                All
                <img src="icon/arrow-right.svg" alt="See all" class="icon">
            </a>
        </div>
        
        <div class="card-container">
            <div class="card-row">
                <div class="card">
                    <div class="card-image">
                        <img src="background/kuala-lumpur.png" alt="Kuala Lumpur">
                    </div>
                    <div class="card-content">
                        <div class="card-header">
                            <h4 class="card-title">Kuala Lumpur, <span class="highlight">Malaysia</span></h4>
                            <span class="card-price">RM 300</span>
                        </div>
                        <p class="card-description">Home to the iconic Petronas Towers and a vibrant city center.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="background/marina-bay.png" alt="Marina Bay">
                    </div>
                    <div class="card-content">
                        <div class="card-header">
                            <h4 class="card-title">Marina Bay, <span class="highlight">Singapore</span></h4>
                            <span class="card-price">RM 400</span>
                        </div>
                        <p class="card-description">Singapore's iconic waterfront hub of attractions and luxury.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-image">
                        <img src="background/langkawi.png" alt="Langkawi">
                    </div>
                    <div class="card-content">
                        <div class="card-header">
                            <h4 class="card-title">Langkawi, <span class="highlight">Malaysia</span></h4>
                            <span class="card-price">RM 200</span>
                        </div>
                        <p class="card-description">An island paradise of beaches and natural wonders.</p>
                    </div>
                </div>
            </div>
            
            <div class="card-row">
                <div class="card">
                    <div class="card-image">
                        <img src="background/fujiyoshida.png" alt="Fujiyoshida">
                    </div>
                    <div class="card-content">
                        <div class="card-header">
                            <h4 class="card-title">Fujiyoshida, <span class="highlight">Japan</span></h4>
                            <span class="card-price">RM 1,000</span>
                        </div>
                        <p class="card-description">A charming city at the base of Mount Fuji, known for its rich cultural heritage, iconic Chureito Pagoda, and stunning views of Japan's most famous mountain.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script src="js/script.js"></script>
</body>
</html>
