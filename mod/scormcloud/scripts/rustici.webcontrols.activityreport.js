
function BuildActivityReport(data) {
	//alert(data.find('registrationreport').children('activity').attr('id'));
	
    render(data.find('registrationreport').children('activity'), $('#report'));
    makeCollapseableTreeFromUnorderedList('report');
}

// Recursively renders the activity and it's children as nested unordered lists
function render(activity, parent) {
    $('<span class="activityTitle" >' + activity.attr('id') + '</span>').appendTo(parent);
    var ul = $('<ul>')
        .append(fmtListItem('Satisfied', activity.children('satisfied').text()))
        .append(fmtListItem('Completed', activity.children('completed').text()))
        .append(fmtListItem('Progress Status', activity.children('progressstatus').text()))
        .append(fmtListItem('Atttempts', activity.children('attempts').text()))
        .append(fmtListItem('Suspended', activity.children('suspended').text()))
        .append(fmtListObjectiveItems(activity.children('objectives')))
        .append(fmtRuntime(activity.children('runtime')))
        .appendTo(parent);

    // If child activities are defined, render them as well
    if (activity.children !== undefined && activity.children !== null && activity.children !== "") {
        $(activity).children('children').children('activity').each(function() {
            render($(this), $('<li>').appendTo(ul));
        });
    }
}

// Helper to print name/value pairs of activity data
function fmtListItem(name, value) {

    if (value === undefined || value === null) {
        value = "";
    }

    return "<li>" + name + ": <span class='dataValue'>" + value + "</span></li>";
}

// Returns the html of one or more lists items representing objective data
function fmtListObjectiveItems(objectives) {

    if (objectives === undefined) {
        return "";
    }   

    var result = "";

    $(objectives).children('objective').each(function(index) {

        result = result + '<li>' +
            $('<li>')
            .append('Activity Objective #' + (index+1))
            .append($('<ul>')
                .append(fmtListItem('Id', $(this).attr('id')))
                .append(fmtListItem('Measure Status', $(this).children('measurestatus').text()))
                .append(fmtListItem('Normalized Measure', $(this).children('normalizedmeasure').text()))
                .append(fmtListItem('Progress Measure', $(this).children('progressstatus').text()))
                .append(fmtListItem('Satisfied Status', $(this).children('satisfiedstatus').text()))
            )
            .html() +  '</li>';
    });

    return result;
}


// Returns the html of the runtime data if it exists
function fmtRuntime(runtime) {

    if (runtime === undefined) {
        return "";

    } else {

        return $('<li>')
            .append('Runtime Data')
            .append($('<ul>')
                .append(fmtListItem('cmi.completion_status', runtime.children('completion_status').text()))
                .append(fmtListItem('cmi.credit', runtime.children('credit').text()))
                .append(fmtListItem('cmi.entry', runtime.children('entry').text()))
                .append(fmtListItem('cmi.exit', runtime.children('exit').text()))
                .append(fmtLearnerPreference(runtime.children('learnerpreference')))
                .append(fmtListItem('cmi.location', runtime.children('location').text()))
                .append(fmtListItem('cmi.mode', runtime.children('mode').text()))
                .append(fmtListItem('cmi.progress_measure', runtime.children('progress_measure').text()))
                .append(fmtListItem('cmi.score_scaled', runtime.children('score_scaled').text()))
                .append(fmtListItem('cmi.score_raw', runtime.children('score_raw').text()))
                .append(fmtListItem('cmi.score_min', runtime.children('score_min').text()))
                .append(fmtListItem('cmi.score_max', runtime.children('score_max').text()))
                .append(fmtListItem('cmi.total_time', runtime.children('total_time').text()))
                .append(fmtListItem('Total Time Tracked by SCORM Engine', runtime.children('timetracked').text()))
                .append(fmtListItem('cmi.success_status', runtime.children('success_status').text()))
                .append(fmtListItem('cmi.suspend_data', runtime.children('suspend_data').text()))
                .append(fmtInteractions(runtime.children('interactions').text()))
                .append(fmtRtObjectives(runtime.children('objectives')))
                .append(fmtComments(runtime.children('comments_from_learner').text(), false))
                .append(fmtComments(runtime.children('comments_from_lms').text(), true))
                .append(fmtStaticData(runtime.children('static')))
             )
    }
}

function fmtLearnerPreference(learner_preference) {

    return $('<li>')
        .append('cmi.learner_preference')
        .append($('<ul>')
            .append(fmtListItem('cmi.learner_preference.audio_level', learner_preference.children('audio_level').text()))
            .append(fmtListItem('cmi.learner_preference.language', learner_preference.children('language').text()))
            .append(fmtListItem('cmi.learner_preference.delivery_speed', learner_preference.children('delivery_speed').text()))
            .append(fmtListItem('cmi.learner_preference.audio_captioning', learner_preference.children('audio_captioning').text()))
         )
}

function fmtStaticData(nstatic) {

    return $('<li>')
        .append('Static Data')
        .append($('<ul>')
            .append(fmtListItem('cmi.completion_threshold', nstatic.children('completion_threshold').text()))
            .append(fmtListItem('cmi.launch_data', nstatic.children('launch_data').text()))
            .append(fmtListItem('cmi.learner_id', nstatic.children('learner_id').text()))
            .append(fmtListItem('cmi.learner_name', nstatic.children('learner_name').text()))
            .append(fmtListItem('cmi.max_time_allowed', nstatic.children('max_time_allowed').text()))
            .append(fmtListItem('cmi.scaled_passing_score', nstatic.children('scaled_passing_score').text()))
            .append(fmtListItem('cmi.time_limit_action', nstatic.children('time_limit_action').text()))
         )
}

// Returns the html of one or more lists items representing objective data
function fmtInteractions(interactions) {

    if (interactions === undefined || interactions == null || interactions == "") {
        return "";
    }   

    var result = "";

    $(interactions).children('interaction').each(function(index) {

        result = result + '<li>' +
            $('<li>')
            .append('cmi.interactions.' + index)
            .append($('<ul>')
                .append(fmtListItem('cmi.interactions.' + index + '.id', $(this).id))
                .append(fmtListItem('cmi.interactions.' + index + '.type', $(this).type))
                .append(fmtListItem('cmi.interactions.' + index + '.timestamp', $(this).timestamp))
                .append(fmtCorrectResponses('cmi.interactions.' + index + '.correct_responses.', $(this).correct_responses))
                .append(fmtListItem('cmi.interactions.' + index + '.weighting', $(this).weighting))
                .append(fmtListItem('cmi.interactions.' + index + '.learner_response', $(this).learner_response))
                .append(fmtListItem('cmi.interactions.' + index + '.result', $(this).result))
                .append(fmtListItem('cmi.interactions.' + index + '.latency', $(this).latency))
                .append(fmtListItem('cmi.interactions.' + index + '.description', $(this).description))
            )
            .html() + '</li>';

    });

    return result;
}

// Returns the html of one or more lists items representing objective data
function fmtComments(comments, fromLms) {

    if (comments === undefined || comments == null || comments == "") {
        return "";
    }   

    if (fromLms) {
        var commentType = "comments_from_lms";
    } else { 
        var commentType = "comments_from_learner";
    }

    var result = "";

    $(comments).children('comment').each(function(index) {

        result = result + '<li>' +
            $('<li>')
            .append('cmi.' + commentType + '.' + index)
            .append($('<ul>')
                .append(fmtListItem('cmi.' + commentType + '.' + index + '.comment', $(this).value))
                .append(fmtListItem('cmi.' + commentType + '.' + index + '.location', $(this).location))
                .append(fmtListItem('cmi.' + commentType + '.' + index + '.timestamp', $(this).timestamp))
            )
            .html() + '</li>';

    });

    return result;
}

function fmtCorrectResponses(title, correctResponses) {

    if (correctResponses === undefined || correctResponses == null || correctResponses == "") {
        return "";
    }   

    var result = "";

    $(correctResponses).children('response').each(function(index) {

        result = result + 
            $('<li>')
            .append(fmtListItem(title + index + '.pattern', $(this).id))
            .html();

    });

    return result;
}

// Returns the html of one or more lists items representing objective data
function fmtRtObjectives(objectives) {

    if (objectives === undefined || objectives == null || objectives == '') {
        return "";
    }   

    var result = "";

    $(objectives).children('objective').each(function(index) {

        result = result + '<li>' +
            $('<li>')
            .append('cmi.objectives.' + index)
            .append($('<ul>')
                .append(fmtListItem('cmi.objectives.' + index + '.id', $(this).id))
                .append(fmtListItem('cmi.objectives.' + index + '.score.scaled', $(this).score_scaled))
                .append(fmtListItem('cmi.objectives.' + index + '.score.raw', $(this).score_raw))
                .append(fmtListItem('cmi.objectives.' + index + '.score.min', $(this).score_min))
                .append(fmtListItem('cmi.objectives.' + index + '.score.max', $(this).score_max))
                .append(fmtListItem('cmi.objectives.' + index + '.success_status', $(this).success_status))
                .append(fmtListItem('cmi.objectives.' + index + '.completion_status', $(this).completion_status))
                .append(fmtListItem('cmi.objectives.' + index + '.progress_measure', $(this).progress_measure))
                .append(fmtListItem('cmi.objectives.' + index + '.description', $(this).description))
            )
            .html() + '</li>';

    });

    return result;
}


// Applies tree control type dhtml collapse/expand functionality to 
// the unordered lists within the specified div.
function makeCollapseableTreeFromUnorderedList(divName) {

  $('#' + divName + ' li:has(ul)')  
    .click(function(event){   
      if (this == event.target) { 
        if ($(this).children().is(':hidden')) {   
          $(this) 
            .css('list-style-image','url(images/minus.gif)') 
            .children().show(); 
        } else { 
          $(this) 
            .css('list-style-image','url(images/plus.gif)') 
            .children().not('span').hide(); 
        } 
      } 
      return false;   
    })
    .css('cursor','pointer')   
    .click();

  $('li:not(:has(ul))').css({   
    cursor: 'default', 
    'list-style-image':'none' 
  });
}

