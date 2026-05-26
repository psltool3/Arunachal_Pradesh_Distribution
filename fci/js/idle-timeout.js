/**
 * Idle Session Timeout Handler
 * Automatically logs out users after 1 hour of inactivity
 */

(function() {
    // Timeout duration in milliseconds (1 hour = 3600000 ms)
    var TIMEOUT_DURATION = 3600000; // 1 hour
    var WARNING_DURATION = 3300000; // 55 minutes (5 minutes before timeout)
    
    var idleTimer = null;
    var warningTimer = null;
    var warningShown = false;
    
    // Events that reset the idle timer
    var activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    
    // Function to redirect to login page
    function logoutUser() {
        // Clear any existing timers
        clearTimeout(idleTimer);
        clearTimeout(warningTimer);
        
        // Destroy session and redirect
        window.location.href = 'api/Logout.php';
    }
    
    // Function to show warning message
    function showWarning() {
        if (!warningShown) {
            warningShown = true;
            
            // Create warning modal
            var warningDiv = document.createElement('div');
            warningDiv.id = 'idle-timeout-warning';
            warningDiv.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 10000; display: flex; align-items: center; justify-content: center;';
            
            var warningBox = document.createElement('div');
            warningBox.style.cssText = 'background: white; padding: 30px; border-radius: 8px; max-width: 500px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.3);';
            
            warningBox.innerHTML = '<h3 style="margin-top: 0; color: #d9534f;">Session Timeout Warning</h3>' +
                '<p style="margin: 20px 0;">Your session will expire in 5 minutes due to inactivity.</p>' +
                '<p style="margin: 20px 0;">Click "Stay Logged In" to continue your session, or you will be automatically logged out.</p>' +
                '<button id="stay-logged-in-btn" style="background-color: #5cb85c; color: white; border: none; padding: 10px 30px; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px;">Stay Logged In</button>' +
                '<button id="logout-now-btn" style="background-color: #d9534f; color: white; border: none; padding: 10px 30px; border-radius: 4px; cursor: pointer; font-size: 16px;">Logout Now</button>';
            
            warningDiv.appendChild(warningBox);
            document.body.appendChild(warningDiv);
            
            // Add event listeners to buttons
            document.getElementById('stay-logged-in-btn').addEventListener('click', function() {
                document.body.removeChild(warningDiv);
                warningShown = false;
                resetTimer();
            });
            
            document.getElementById('logout-now-btn').addEventListener('click', function() {
                logoutUser();
            });
        }
    }
    
    // Function to reset the idle timer
    function resetTimer() {
        // Clear existing timers
        clearTimeout(idleTimer);
        clearTimeout(warningTimer);
        
        // Remove warning if shown
        var existingWarning = document.getElementById('idle-timeout-warning');
        if (existingWarning) {
            document.body.removeChild(existingWarning);
            warningShown = false;
        }
        
        // Set warning timer (5 minutes before timeout)
        warningTimer = setTimeout(showWarning, WARNING_DURATION);
        
        // Set logout timer
        idleTimer = setTimeout(logoutUser, TIMEOUT_DURATION);
    }
    
    // Initialize timers when page loads
    function init() {
        // Set up event listeners for user activity
        activityEvents.forEach(function(event) {
            document.addEventListener(event, resetTimer, true);
        });
        
        // Start the timer
        resetTimer();
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        init();
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }
})();
