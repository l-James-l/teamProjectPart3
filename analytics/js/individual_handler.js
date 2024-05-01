function fetchUserData(userID, callback) {
    fetch(`apis/getUserDetails.php?userID=${userID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            callback(data);
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
        });
}

function updateUserData(userData){
    document.getElementById('fullName').innerText = userData.data.userDetails.fullName;
    document.getElementById('role').innerText = userData.data.userDetails.role;

    document.getElementById('percentageNumber').innerText = userData.data.statistics.overallCompletion;
    document.getElementById('percentageNumber').setAttribute('title', `Hours Done: ${userData.data.statistics.hoursDone}, Hours Left: ${userData.data.statistics.hoursLeft}`);
    document.getElementById('hoursNumber').innerText = userData.data.statistics.hoursLeft;
    document.getElementById('taskProjectInfo').innerText = `Current Tasks - ${userData.data.statistics.taskCount} across ${userData.data.statistics.projectCount} projects`;

    var circlePercentageElement = document.getElementById('circlePercentage');
    if (userData.data.statistics.overallCompletion < 40) {
        circlePercentageElement.style.backgroundColor = 'red';
    } else if (userData.data.statistics.overallCompletion >= 40 && userData.data.statistics.overallCompletion <= 70) {
        circlePercentageElement.style.backgroundColor = 'yellow';
    } else {
        circlePercentageElement.style.backgroundColor = 'green';
    }
}


