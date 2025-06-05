<style>
    /* Google Fonts Import */
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

/* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Nunito Sans', sans-serif;
    background-color: #031E2F;
    color: #FFFFFF;
    line-height: 1.364;
}

/* Feedback Section Styles */
.feedback-section {
    background-color: #031E2F;
    padding: 64px;
    width: 100%;
}

.feedback-container {
    max-width: 1234px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Reviews Header Styles */
.reviews-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
    width: 100%;
    margin-bottom: 0;
}

.reviews-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 20px;
    line-height: 1.263em;
    color: #FFFFFF;
    margin: 0;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 16px;
}

.rating-score {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 50px;
    line-height: 1.263em;
    color: #FFFFFF;
}

.rating-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: center;
    justify-content: center;
}

.rating-stars {
    display: flex;
    gap: 4px;
}

.rating-stars i {
    font-size: 18px;
    color: #fff;
    background: linear-gradient(180deg, #605DEC 0%, #2A26D9 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.rating-label {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 20px;
    line-height: 1.219em;
    color: #FFFFFF;
}

/* Divider Styles */
.reviews-divider,
.review-divider {
    width: 100%;
    height: 0.5px;
    background-color: #FFFFFF;
    opacity: 0.25;
}

/* Reviews List Styles */
.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.review-item {
    width: 100%;
}

.review-content {
    display: flex;
    gap: 16px;
    width: 100%;
    position: relative;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    flex-shrink: 0;
    object-fit: cover;
}

.review-text {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.review-header {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.user-name {
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 1.219em;
    color: #FFFFFF;
}

.separator {
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 1.219em;
    color: #FFFFFF;
}

.review-rating {
    display: flex;
    gap: 2px;
}

.review-rating i {
    font-size: 14px;
    color: #fff;
    background: linear-gradient(180deg, #605DEC 0%, #2A26D9 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.review-comment {
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    font-size: 14px;
    line-height: 1.219em;
    color: #FFFFFF;
    margin: 0;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .feedback-section {
        padding: 40px 32px;
    }
    
    .feedback-container {
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .feedback-section {
        padding: 40px 20px;
    }
    
    .reviews-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .rating-display {
        gap: 12px;
    }
    
    .rating-score {
        font-size: 36px;
    }
    
    .rating-label {
        font-size: 16px;
    }
    
    .review-content {
        gap: 12px;
    }
    
    .user-avatar {
        width: 35px;
        height: 35px;
    }
    
    .review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
}

@media (max-width: 480px) {
    .reviews-title {
        font-size: 18px;
    }
    
    .rating-score {
        font-size: 28px;
    }
    
    .rating-label {
        font-size: 14px;
    }
    
    .user-name,
    .separator,
    .review-comment {
        font-size: 12px;
    }
    
    .review-rating i {
        font-size: 12px;
    }
}    
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <section class="feedback-section">
        <div class="feedback-container">
            <!-- Reviews Header -->
            <div class="reviews-header">
                <h2 class="reviews-title">Reviews</h2>
                <div class="rating-display">
                    <span class="rating-score">4.2</span>
                    <div class="rating-details">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="rating-label">Very good</span>
                    </div>
                </div>
            </div>
            
            <div class="reviews-divider"></div>
            
            <!-- Reviews List -->
            <div class="reviews-list">
                <!-- Review 1 -->
                <div class="review-item">
                    <div class="review-content">
                        <img src="icon/profile.svg" alt="Omar Siphron" class="user-avatar">
                        <div class="review-text">
                            <div class="review-header">
                                <span class="user-name">Omar Siphron</span>
                                <span class="separator">|</span>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="review-comment">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                    </div>
                </div>
                
                <div class="review-divider"></div>
                
                <!-- Review 2 -->
                <div class="review-item">
                    <div class="review-content">
                        <img src="icon/profile.svg" alt="Cristofer Ekstrom Bothman" class="user-avatar">
                        <div class="review-text">
                            <div class="review-header">
                                <span class="user-name">Cristofer Ekstrom Bothman</span>
                                <span class="separator">|</span>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="review-comment">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                    </div>
                </div>
                
                <div class="review-divider"></div>
                
                <!-- Review 3 -->
                <div class="review-item">
                    <div class="review-content">
                        <img src="icon/profile.svg" alt="Kaiya Lubin" class="user-avatar">
                        <div class="review-text">
                            <div class="review-header">
                                <span class="user-name">Kaiya Lubin</span>
                                <span class="separator">|</span>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="review-comment">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                    </div>
                </div>
                
                <div class="review-divider"></div>
                
                <!-- Review 4 -->
                <div class="review-item">
                    <div class="review-content">
                        <img src="icon/profile.svg" alt="Erin Septimus" class="user-avatar">
                        <div class="review-text">
                            <div class="review-header">
                                <span class="user-name">Erin Septimus</span>
                                <span class="separator">|</span>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="review-comment">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                    </div>
                </div>
                
                <div class="review-divider"></div>
                
                <!-- Review 5 -->
                <div class="review-item">
                    <div class="review-content">
                        <img src="icon/profile.svg" alt="Terry George" class="user-avatar">
                        <div class="review-text">
                            <div class="review-header">
                                <span class="user-name">Terry George</span>
                                <span class="separator">|</span>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="review-comment">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                    </div>
                </div>
                
                <div class="review-divider"></div>
            </div>
        </div>
    </section>
</body>
</html>
