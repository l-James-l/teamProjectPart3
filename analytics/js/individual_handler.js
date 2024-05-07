function fetchUserData(userID, callback) {
    fetch(`api/getUserDetails.php?userID=${userID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Parsed JSON data:', data);
            callback(data);
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
        });
}

function updateUserData(userData) {
    document.getElementById('fullName').innerText = userData.data.userDetails.fullName;
    document.getElementById('role').innerText = userData.data.userDetails.role;

    var completionPercentage = userData.data.statistics.overallCompletion;
    var percentageElement = document.getElementById('percentageNumber');

    document.getElementById('hoursNumber').innerText = userData.data.statistics.hoursLeft;
    document.getElementById('taskProjectInfo').innerText = `${userData.data.statistics.taskCount} tasks assigned across ${userData.data.statistics.projectCount} projects`;

    if (completionPercentage < 40) {
        percentageElement.style.color = 'red';
    } else if (completionPercentage >= 40 && completionPercentage <= 70) {
        percentageElement.style.color = 'yellow';
    } else {
        percentageElement.style.color = 'green';
    }
}


