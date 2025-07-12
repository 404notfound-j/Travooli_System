// Airline Reviews JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const flightSearch = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');
    const airline_id = flightSearch.airline_id;
    
    if (airline_id) {
        loadAirlineReviews(airline_id);
    } else {
        console.error('No airline_id found in sessionStorage');
        document.getElementById('no-reviews-message').textContent = 'No reviews available for this airline yet.';
    }
    
    // Function to load airline reviews
    function loadAirlineReviews(airline_id) {
        fetch(`get_airline_reviews.php?airline_id=${airline_id}`)
            .then(response => response.json())
            .then(data => {
                // Update average rating
                document.getElementById('avg-rating').textContent = data.avg_rating;
                document.getElementById('feedback-avg-rating').textContent = data.avg_rating;
                
                // Update rating text
                const ratingText = document.getElementById('rating-text');
                const feedbackRatingLabel = document.getElementById('feedback-rating-label');
                
                if (data.total_reviews > 0) {
                    // Only show the rating text without the review count
                    ratingText.textContent = data.rating_text;
                    // Keep the full text with count for the detailed feedback section
                    feedbackRatingLabel.textContent = `${data.rating_text} (${data.total_reviews} reviews)`;
                } else {
                    ratingText.textContent = 'No reviews yet';
                    feedbackRatingLabel.textContent = 'No reviews yet';
                }
                
                // Update rating stars
                const starsContainer = document.getElementById('feedback-rating-stars');
                starsContainer.innerHTML = '';
                
                for (let i = 1; i <= 5; i++) {
                    const star = document.createElement('i');
                    if (i <= data.avg_rating) {
                        star.className = 'fas fa-star';
                    } else if (i - 0.5 <= data.avg_rating) {
                        star.className = 'fas fa-star-half-alt';
                    } else {
                        star.className = 'far fa-star';
                    }
                    starsContainer.appendChild(star);
                }
                
                // Display reviews
                const reviewsList = document.getElementById('reviews-list');
                reviewsList.innerHTML = '';
                
                if (data.feedbacks && data.feedbacks.length > 0) {
                    data.feedbacks.forEach((feedback, index) => {
                        const reviewItem = document.createElement('div');
                        reviewItem.className = 'review-item';
                        
                        const reviewContent = document.createElement('div');
                        reviewContent.className = 'review-content';
                        
                        const avatar = document.createElement('img');
                        avatar.src = 'icon/profile.svg';
                        avatar.alt = feedback.fst_name + ' ' + feedback.lst_name;
                        avatar.className = 'user-avatar';
                        
                        const reviewText = document.createElement('div');
                        reviewText.className = 'review-text';
                        
                        const reviewHeader = document.createElement('div');
                        reviewHeader.className = 'review-header';
                        
                        const userName = document.createElement('span');
                        userName.className = 'user-name';
                        userName.textContent = feedback.fst_name + ' ' + feedback.lst_name;
                        
                        const separator = document.createElement('span');
                        separator.className = 'separator';
                        separator.textContent = '|';
                        
                        const reviewRating = document.createElement('div');
                        reviewRating.className = 'review-rating';
                        
                        for (let i = 1; i <= 5; i++) {
                            const star = document.createElement('i');
                            star.className = i <= feedback.rating ? 'fas fa-star' : 'far fa-star';
                            reviewRating.appendChild(star);
                        }
                        
                        const reviewComment = document.createElement('p');
                        reviewComment.className = 'review-comment';
                        reviewComment.textContent = feedback.feedback;
                        
                        reviewHeader.appendChild(userName);
                        reviewHeader.appendChild(separator);
                        reviewHeader.appendChild(reviewRating);
                        
                        reviewText.appendChild(reviewHeader);
                        reviewText.appendChild(reviewComment);
                        
                        reviewContent.appendChild(avatar);
                        reviewContent.appendChild(reviewText);
                        
                        reviewItem.appendChild(reviewContent);
                        reviewsList.appendChild(reviewItem);
                        
                        if (index < data.feedbacks.length - 1) {
                            const divider = document.createElement('div');
                            divider.className = 'review-divider';
                            reviewsList.appendChild(divider);
                        }
                    });
                } else {
                    const noReviews = document.createElement('p');
                    noReviews.textContent = 'No reviews available for this airline yet.';
                    reviewsList.appendChild(noReviews);
                }
                
                // Check if user can leave feedback
                if (data.can_leave_feedback) {
                    const formContainer = document.getElementById('feedback-form-container');
                    formContainer.innerHTML = `
                        <h3>Leave Your Review</h3>
                        <form id="feedbackForm" class="feedback-form">
                            <input type="hidden" name="action" value="submitFeedback">
                            <input type="hidden" name="airline_id" value="${airline_id}">
                            <input type="hidden" name="f_book_id" value="${data.booking_id}">
                            
                            <div class="rating-input">
                                <label>Your Rating:</label>
                                <div class="star-rating">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </div>
                                <input type="hidden" name="rating" id="ratingInput" value="0">
                            </div>
                            
                            <div class="feedback-input">
                                <label for="feedbackText">Your Review:</label>
                                <textarea id="feedbackText" name="feedback" rows="4" placeholder="Share your experience with this airline..."></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="submit-btn">Submit Review</button>
                            </div>
                            
                            <div class="form-message"></div>
                        </form>
                    `;
                    
                    const reviewsDivider = document.createElement('div');
                    reviewsDivider.className = 'reviews-divider';
                    formContainer.appendChild(reviewsDivider);
                    
                    // Add event listeners for the feedback form
                    setupFeedbackForm();
                }
            })
            .catch(error => {
                console.error('Error loading airline reviews:', error);
                document.getElementById('no-reviews-message').textContent = 'Failed to load reviews. Please try again later.';
            });
    }
    
    // Function to set up feedback form event listeners
    function setupFeedbackForm() {
        // Star rating functionality
        const stars = document.querySelectorAll('.star-rating i');
        const ratingInput = document.getElementById('ratingInput');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const rating = this.getAttribute('data-rating');
                
                // Reset all stars
                stars.forEach(s => {
                    s.className = 'far fa-star';
                });
                
                // Fill stars up to the hovered one
                for (let i = 0; i < rating; i++) {
                    stars[i].className = 'fas fa-star';
                }
            });
            
            star.addEventListener('mouseout', function() {
                const currentRating = ratingInput.value;
                
                // Reset all stars
                stars.forEach(s => {
                    s.className = 'far fa-star';
                });
                
                // Fill stars based on current rating
                for (let i = 0; i < currentRating; i++) {
                    stars[i].className = 'fas fa-star';
                }
            });
            
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                
                // Fill stars up to the selected one
                stars.forEach((s, index) => {
                    s.className = index < rating ? 'fas fa-star' : 'far fa-star';
                });
            });
        });
        
        // Form submission
        const feedbackForm = document.getElementById('feedbackForm');
        if (feedbackForm) {
            feedbackForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const formMessage = document.querySelector('.form-message');
                
                // Validate form
                const rating = formData.get('rating');
                const feedback = formData.get('feedback');
                
                if (rating < 1 || rating > 5) {
                    formMessage.innerHTML = '<div class="error-message">Please select a rating</div>';
                    return;
                }
                
                if (!feedback.trim()) {
                    formMessage.innerHTML = '<div class="error-message">Please enter your review</div>';
                    return;
                }
                
                // Submit form via AJAX
                fetch('submit_flight_feedback.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        formMessage.innerHTML = '<div class="success-message">Thank you for your feedback!</div>';
                        feedbackForm.reset();
                        
                        // Reload page after 2 seconds to show the new review
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        formMessage.innerHTML = `<div class="error-message">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    formMessage.innerHTML = '<div class="error-message">An error occurred. Please try again.</div>';
                    console.error('Error:', error);
                });
            });
        }
    }
}); 