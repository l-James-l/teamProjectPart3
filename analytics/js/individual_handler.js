function fetchUserData(userID, callback) {
    fetch(`apis/getUserDetails.php?userID=${userID}`)
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

    var hoursCompleted = userData.data.statistics.hoursDone;
    var hoursRemaining = userData.data.statistics.hoursLeft;
    var hoursSummaryElement = document.getElementById('overviewHoursSummary');
    hoursSummaryElement.innerText = `${hoursCompleted} hours completed, ${hoursRemaining} hours remaining`;

    var completionPercentage = userData.data.statistics.overallCompletion;
    var percentageElement = document.getElementById('overviewPercentageNumber');
    percentageElement.innerText = completionPercentage + '%'; 

    if (completionPercentage < 40) {
        percentageElement.style.color = 'red';
    } else if (completionPercentage >= 40 && completionPercentage <= 70) {
        percentageElement.style.color = 'yellow';
    } else {
        percentageElement.style.color = 'green';
    }

    var taskCount = userData.data.statistics.taskCount;
    var projectCount = userData.data.statistics.projectCount;
    var taskProjectInfoElement = document.getElementById('overviewTaskProjectInfo');
    taskProjectInfoElement.innerText = `${taskCount} tasks assigned across ${projectCount} projects`;
}


