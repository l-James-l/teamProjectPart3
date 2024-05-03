function get_project_from_api(id, page) {
    let data = {
        project_ID: id
    }
    if (page == "overview") {
        data["search"] = document.getElementById("searchbar").value, 
        data["sort_value"] = document.getElementById("sortValue").value,
        data["sort_order"] = document.getElementById("sortOrder").value,
        data["task_filter_milestone"] =  document.getElementById("milestoneToggleValue").value % 2 == 0 ? false : true,
        data["complete_filter"] =  document.getElementById("completeToggleValue").value % 2 == 0 ? false : true,
        data["incomplete_filter"] =  document.getElementById("incompleteToggleValue").value % 2 == 0 ? false : true
    } else if (page == "users") {
        data["search"] = document.getElementById("searchbar").value
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
            if (page == "overview") {
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

            } else if (page == "users") {
                google.charts.load('current',{packages:['bar']});
                google.charts.setOnLoadCallback(function () {drawHoursBarChart(response["message"]["user_assignment"])});
            } else if (page == "progress") {
                google.charts.load('current', {'packages':['line']});
                google.charts.setOnLoadCallback(function () {drawprogressLineChart(response["message"]["progress_log"])});
            }
        },
        error: function (error) {
            console.error(error); // log error to the console
        }
    })
}


function drawprogressLineChart(progressData) {
    var data = new google.visualization.DataTable();
    data.addColumn('date', 'Date');
    data.addColumn('number', 'Day to Day');
    data.addColumn('number', 'Cumulative');

    var running_sum = 0
    processedDataArray = []
    progressData.forEach(row => {
        running_sum = running_sum + parseInt(row["hours_sum"])
        processedDataArray.push([new Date(row["date"]), parseInt(row["hours_sum"]), running_sum])
    })
    data.addRows(processedDataArray)

    var options = {
        chart: {
          title: 'Project Progress Over Time',
          subtitle: 'in hours commited'
        },
        height: 500
    };

    if (processedDataArray.length > 0) {
        var chart = new google.charts.Line(document.getElementById('progress_line_chart'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    } else {
        document.getElementById('progress_line_chart').innerHTML = "There has been no progress logged for this project yet."
    }
}


function drawHoursBarChart(userData) {
    const all_graphs_container = document.getElementById("users_graphs_container")
    all_graphs_container.innerHTML = ""

    let i = 1
    Object.keys(userData).forEach(username => {
        let userTasks = userData[username]

        let data = [[username, "Estimated Duration", "Logged Hours"]]
        Object.values(userTasks).forEach(task => {
            data.push([task["title"], parseInt(task["est_length"]), parseInt(task["total_logged_hrs"])])
        });
        data = google.visualization.arrayToDataTable(data);

        var options = {
            chart: {
                title: 'Task, Assigned Hours and Logged Hours for ' + username,
              },
            bars: 'vertical'
          };

        let accordion_item = document.createElement("div")
        accordion_item.classList.add("accordion-item")

        let card_header = document.createElement("h2")
        card_header.classList.add("accordion-header")
        card_header.id = 'header-x'+i.toString()

        let collapse_button = document.createElement("button")
        collapse_button.classList.add("accordion-button")
        collapse_button.type = "button"
        collapse_button.setAttribute("data-bs-toggle", "collapse")
        collapse_button.setAttribute("data-bs-target", "#content-x"+i.toString())
        collapse_button.setAttribute("aria-expanded", "true")
        collapse_button.setAttribute("aria-controls", "content-x"+i.toString())
        collapse_button.innerHTML = username

        card_header.appendChild(collapse_button)
        accordion_item.appendChild(card_header)

        let content_div = document.createElement("div")
        content_div.classList.add("accordion-collapse", "collapse", "show")
        content_div.id = "content-x"+i.toString()
        content_div.setAttribute("aria-labelledby", "panelsStayOpen-headingOne")

        let this_graph_div = document.createElement("div")
        this_graph_div.style["width"] = "-webkit-fill-available"
        this_graph_div.style["height"] = String(Math.max(150, Object.keys(userTasks).length * 100)) + "px"
        this_graph_div.classList.add("accordion-body")
        content_div.appendChild(this_graph_div)
        accordion_item.appendChild(content_div)
        
        all_graphs_container.appendChild(accordion_item)

        var chart = new google.charts.Bar(this_graph_div);
        chart.draw(data, options);

        i++
    });
}

function update_task_display(tasksList) {
    let task_container = document.getElementById("task-container")
    task_container.innerHTML = ""
    tasksList.forEach(task => {
        let task_div = document.createElement("div")
        task_div.classList.add("task-box", "bg-light", "border", "rounded", "p-3", "mb-2", "task-info")

        let task_title = document.createElement("h5")
        task_title.classList.add("task-name")
        if (task["is_milestone"]) {
            task_title.innerHTML = "Milestone: " + task["task_title"]
        } else {
            task_title.innerHTML = "Task: " + task["task_title"]
        }
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