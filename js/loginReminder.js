window.showLoginReminder = function() {
    // Create modal container if it doesn't exist
    let modal = document.getElementById('loginReminderModal');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'loginReminderModal';
        modal.className = 'login-modal-overlay';
        modal.innerHTML = `
            <div class="login-modal-dialog">
                <div class="login-modal-content">
                    <div class="login-modal-header">
                        <h3>Please Sign In</h3>
                    </div>
                    <div class="login-modal-body">
                        <p>You must be signed in to book a hotel room.</p>
                        <div style="margin: 16px 0;">
                            <a href="signIn.php" class="login-modal-link">Sign In</a><br>
                            <a href="signUp.php" class="login-modal-link">Sign Up</a>
                        </div>
                    </div>
                    <div class="login-modal-footer">
                        <button type="button" class="login-btn-secondary" onclick="closeLoginReminder()">Cancel</button>
                    </div>
                </div>
            </div>
        `;
        // Add modal styles only once
        if (!document.getElementById('loginReminderModalStyle')) {
            const style = document.createElement('style');
            style.id = 'loginReminderModalStyle';
            style.textContent = `
                .login-modal-overlay {
                    display: flex;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 9999;
                    justify-content: center;
                    align-items: center;
                }
                .login-modal-dialog {
                    width: 90%;
                    max-width: 400px;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                    animation: modalFadeIn 0.3s ease;
                }
                @keyframes modalFadeIn {
                    from { opacity: 0; transform: translateY(-20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .login-modal-content { display: flex; flex-direction: column; }
                .login-modal-header { padding: 20px 20px 0 20px; text-align: center; }
                .login-modal-header h3 { margin: 0; color: #605DEC; font-size: 22px; }
                .login-modal-body { padding: 20px; line-height: 1.5; color: #555; text-align: center; }
                .login-modal-link { color: #605DEC; text-decoration: none; font-weight: 500; font-size: 16px; margin: 0 8px; }
                .login-modal-link:hover { text-decoration: underline; }
                .login-modal-footer { display: flex; justify-content: center; padding: 15px 20px 20px 20px; }
                .login-btn-secondary {
                    background-color: #605DEC;
                    color: #fff;
                    border: 1.5px solid #605DEC;
                    padding: 10px 24px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 16px;
                    font-weight: 600;
                    margin: 0 4px;
                    transition: background 0.2s, color 0.2s, border 0.2s;
                    box-shadow: 0 2px 8px #605dec22;
                }
                .login-btn-secondary:hover {
                    background-color: #4845CC;
                    border-color: #4845CC;
                    color: #fff;
                }
            `;
            document.head.appendChild(style);
        }
        document.body.appendChild(modal);
    } else {
        modal.style.display = 'flex';
    }
    // Prevent body scrolling when modal is open
    document.body.style.overflow = 'hidden';
}

window.closeLoginReminder = function() {
    const modal = document.getElementById('loginReminderModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
};