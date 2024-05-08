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
    document.getElementById('fullName').innerText = "Overview - " + userData.data.userDetails.fullName;
    
    var roleText = '';
    switch (userData.data.userDetails.role) {
        case 'Mgr':
            roleText = 'Manager';
            break;
        case 'Emp':
            roleText = 'Employee';
            break;
        case 'TL':
            roleText = 'Team Leader';
            break;
        default:
            roleText = 'Role Undefined'; 
    }
    document.getElementById('role').innerText = roleText;

    var hoursCompleted = userData.data.statistics.hoursDone;
    var hoursRemaining = userData.data.statistics.hoursLeft;
    var hoursSummaryElement = document.getElementById('overviewHoursSummary');
    hoursSummaryElement.innerText = `${hoursCompleted} hours completed, ${hoursRemaining} hours remaining`;

    var completionPercentage = userData.data.statistics.overallCompletion;
    var percentageElement = document.getElementById('overviewPercentageNumber');
    percentageElement.innerText = completionPercentage + '%';
    percentageElement.style.fontWeight = 'bold';

    if (completionPercentage < 40) {
        percentageElement.style.color = 'red';
    } else if (completionPercentage >= 40 && completionPercentage <= 70) {
        percentageElement.style.color = 'orange';
    } else {
        percentageElement.style.color = 'green';
    }

    var taskCount = userData.data.statistics.taskCount;
    var projectCount = userData.data.statistics.projectCount;
    var taskProjectInfoElement = document.getElementById('overviewTaskProjectInfo');
    taskProjectInfoElement.innerText = `${taskCount} tasks assigned across ${projectCount} projects`;

    updatePieChart(hoursCompleted, hoursRemaining);
}

function updatePieChart(hoursCompleted, hoursRemaining) {
    var ctx = document.getElementById('taskCompletionPieChart').getContext('2d');
    var totalHours = hoursCompleted + hoursRemaining;
    var data = {
        labels: ['Hours Completed', 'Hours Remaining'],
        datasets: [{
            data: [hoursCompleted, hoursRemaining],
            backgroundColor: ['#4CAF50', '#FFC107'],
            borderColor: ['#fff'],
            borderWidth: 1
        }]
    };

    if (window.pieChart) {
        window.pieChart.data.datasets[0].data = [hoursCompleted, hoursRemaining];
        window.pieChart.update();
    } else {
        window.pieChart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
}



