function get_project_from_api(id) {
    let data = {
        project_ID: id,
        task_search: document.getElementById("searchbar").value, 
        sort_value: document.getElementById("sortValue").value,
        sort_order: document.getElementById("sortOrder").value,
        task_filter_milestone: document.getElementById("milestoneToggleValue").value % 2 == 0 ? false : true
    }
    $.ajax({
        url: 'apis/get_project.php',
        method: 'GET',
        data: data,
        success: function (response) {
            console.log(response)
            // display_projects(JSON.parse(response))
            response = JSON.parse(response)
            document.getElementById("project_title").innerHTML = response["message"]["project"]["project_title"]
            document.getElementById("task_count").innerHTML = response["message"]["project"]["task_count"] + " Remaining Tasks"
            document.getElementById("summary-pie").style.setProperty("--p", response["message"]["project"]["total_completion"])
            if (response["message"]["project"]["total_completion"] < 33) {
                document.getElementById("summary-pie").style.setProperty("--c", "red")
            } else if (response["message"]["project"]["total_completion"] < 66) {
                document.getElementById("summary-pie").style.setProperty("--c", "orange")
            } else {
                document.getElementById("summary-pie").style.setProperty("--c", "green")
            }
            document.getElementById("summary-pie").innerHTML = response["message"]["project"]["total_completion"] + "%"
            document.getElementById("summary-label").innerHTML = "A total of " + response["message"]["project"]["total_hours"] + " hours have been assigned to this project. The exected completion date for the project is " + response["message"]["project"]["due_date"] + "."
            update_task_display(response["message"]["tasks"])
        },
        error: function (error) {
            console.error(error); // log error to the console
        }
    })
}

function update_task_display(tasksList) {
    let task_container = document.getElementById("task-container")
    task_container.innerHTML = ""
    tasksList.forEach(task => {
        let task_div = document.createElement("div")
        task_div.classList.add("task-box", "bg-light", "border", "rounded", "p-3", "mb-2", "task-info")

        let task_title = document.createElement("h5")
        task_title.classList.add("task-name")
        task_title.innerHTML = "Task: " + task["task_title"]
        task_div.appendChild(task_title)

        let due_date = document.createElement("p")
        due_date.classList.add("task-due-date")
        due_date.innerHTML = "Due Date: " + task["due_date"]
        task_div.appendChild(due_date)

        let priority = document.createElement("p")
        priority.classList.add("task-priority")
        priority.innerHTML = "Priority: " + task["priority"]
        task_div.appendChild(priority)

        let task_length = document.createElement("p")
        task_length.classList.add("task-length")
        task_length.innerHTML = "Estimated Duration: " + task["est_length"]
        task_div.appendChild(task_length)

        let progress_bar_containter = document.createElement("div")
        progress_bar_containter.classList.add("progress")
        progress_bar_containter.style["height"] = "20px"

        let progress_bar = document.createElement("div")
        progress_bar.role = "progressbar"
        progress_bar.classList.add("progress-bar")
        progress_bar.ariaValueNow = task["completion_percentage"] ? task["completion_percentage"]: 0
        progress_bar.ariaValueMin = 0
        progress_bar.ariaValueMax = 100
        progress_bar.style["width"] = task["completion_percentage"] ? task["completion_percentage"] + "%": 0
        
        progress_bar_containter.appendChild(progress_bar)
        task_div.appendChild(progress_bar_containter)

        task_container.appendChild(task_div)

    })

    // <h5 class="task-name">Task: <?php echo htmlspecialchars($taskRow["task_title"]); ?></h5>
    // <h5 class="project-name">Project: <?php echo htmlspecialchars($taskRow["project_title"]); ?></h5>
    // <p class="task-due-date">Due Date: <?php echo htmlspecialchars($taskRow["due_date"]); ?></p>
    // <p class="task-priority">Priority: <?php echo htmlspecialchars($taskRow["priority"]); ?></p>
    // <p class="task-length">Estimated Length: <?php echo htmlspecialchars($taskRow["est_length"]); ?> hours</p>
    // <div class="progress" style="height: 20px;">
    //     <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($taskRow["completion_percentage"]); ?>%;" aria-valuenow="<?php echo htmlspecialchars($taskRow["completion_percentage"]); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo htmlspecialchars($taskRow["completion_percentage"]); ?>% Complete</div>
    // </div>
}

function toggleFilter(toggle) {
    document.getElementById(toggle+"ToggleValue").value++
    document.getElementById(toggle+"ToggleIcon").classList.toggle("bi-check")
    document.getElementById(toggle+"ToggleIcon").classList.toggle("bi-x")
}


function change_sort_value(new_value) {
    document.getElementById("sortValue").value = new_value
}

function change_sort_order(new_order) {
    document.getElementById("sortOrder").value = new_order
    if (new_order == "DESC") {
        document.getElementById("desc-button").classList.remove("btn-outline-secondary")
        document.getElementById("desc-button").classList.add("btn-secondary")
        document.getElementById("asc-button").classList.remove("btn-secondary")
        document.getElementById("asc-button").classList.add("btn-outline-secondary")
    } else {
        document.getElementById("asc-button").classList.remove("btn-outline-secondary")
        document.getElementById("asc-button").classList.add("btn-secondary")
        document.getElementById("desc-button").classList.remove("btn-secondary")
        document.getElementById("desc-button").classList.add("btn-outline-secondary")
    }
}