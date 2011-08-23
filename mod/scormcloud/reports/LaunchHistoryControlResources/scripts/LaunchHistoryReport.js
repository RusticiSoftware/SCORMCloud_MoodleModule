// parameters
// These parameters can be given values on the page that includes this script
var extConfigurationString = '';


// constants
var SCORM_TRUE_FALSE = "true-false";
var SCORM_CHOICE = "choice";
var SCORM_FILL_IN = "fill-in";
var SCORM_MATCHING = "matching";
var SCORM_PERFORMANCE = "performance";
var SCORM_SEQUENCING = "sequencing";
var SCORM_LIKERT = "likert";
var SCORM_NUMERIC = "numeric";
var SCORM_LONG_FILL_IN = "long-fill-in";
var SCORM_OTHER = "other";

var scormGlossary = 
{
    activity:'Every item in a SCORM manifest is called an "activity". Activities can be "deliverable", or "leafs" when they correspond to a SCO or Asset. Or, activities can be "clusters" when they are aggregations containing child activities.',
    rawscore: 'Were a user to get 5 of 6 questions on a test right, the expected values would be: min=0, max=6, raw=5, scaled=83.33%',
    scaledscore: 'Were a user to get 5 of 6 questions on a test right, the expected values would be: min=0, max=6, raw=5, scaled=83.33%',
    attempt: 'An attempt is when the SCORM player resets the tracking data for an activity and the learner starts fresh.',
    runtimestate: 'The run-time state is the detailed tracking data that the SCO reports to the SCORM player (such as scores and question results). This data is often reset when a new attempt begins.',
    step: 'How a learner responded at a given point in multi-step interaction.',
    optionwithid: 'The identifier of a possible response to a multiple choice question.',
    sessiontime: 'The amount of time that the leaner spent interacting with this activity as reported by the content.',
    incomplete: 'The learner has started, but not yet finished this activity.',
    completed: 'The learner has finished this activity.',
    passed: 'The learner has mastered the content of this activity.',
    failed: 'The learner has not yet mastered the content of this activity.',
    correct: 'The learner responded to a question or interaction in a way that demonstrates mastery.',
    incorrect: 'The learner responded to a question or interaction in a way that demonstrates a lack of understanding.',
    unanticipated: 'The learner responded to a question or interaction in a way that the content did not recognize.',
    neutral: 'The learner responded to a question or interaction in a way that was neither correct nor incorrect.',
    correctnessvalue: "The content reported the result of this interaction as a number representing the learner's performance.",
    viewed: 'As tracked by the SCORM player, the activity was displayed to the learner for this much time.',
    objective: "A learning objective that this content is instructing. An activity can report the learner's progress on many separate learning objectives. (Caution, sometimes content will store purely technical data in objectives.)",
    suspendallandexit: 'The SCORM player is exiting with the intention of letting the learner return back to continue this course.',
    exitall: 'The SCORM player is exiting and the learner will not likely continue this course.',
    choice: 'The next activity was explicitly selected from the table of contents.',
    jump: 'The next activity was explicitly selected from the table of contents by the content.',
    abandon: 'The activity is exiting.',
    abandonall: 'The SCORM player is exiting, the learner will not likely continue this course and the current attempt should not be counted.',
    coursesatisfactionstatus: 'Satisfaction refers to the concept of pass/fail.  Has the user done what is required to have passed the course as a whole?',
    coursecompletionstatus: 'Completion refers to whether the course has been finished.  (Note: a course could be failed and complete.)',
    dummyEndMarker:''
};


// Globals
// So that we only put out the time when it changes 
var emittedTimestamp = '';
var currentPopup = null;

// Initial ready handler,  make every hide_show_div generate loadreport on click
$(function() { 
  $("div.hide_show_div")
                    .css('cursor','pointer') 
                    .click(function() {
                               loadReport('report','', $(this).parent());
                               $(this).find('td.launch_listPrefix').html('-');
                               }); 

}); 

function emitTimestamp(ts, force) {
    // strip away to just report to the minute, and only print when changed
    var hms = ts.match(/^(\d+:\d+):.*$/);
    var timestampStr =  hms ? hms[1] : ts;
    if (!force && timestampStr === emittedTimestamp) {
        timestampStr = ''
    }
    else {
        emittedTimestamp = timestampStr;
    }
    return '<div class="launch_timestamp">' + timestampStr + '</div>';
}

function wrapLogEntry(entry, content, add_class) {
    return '<div class="launch_report_list_entry' + (add_class ? ' ' + add_class : '') + '">' + emitTimestamp($(entry).attr('timestamp'), false) + '<div class="launch_report_list_entry_text">' + content + '</div><div class="launch_report_list_entry_text_invisible">' + content + '</div></div>\n';
}

// If str is a key in the glossary, then wrap it
function mkAbbr(str) {
    if (scormGlossary[str.toLowerCase().replace(/\s*/g, '')]) {
        return '<abbr>' + str + '</abbr>';
    }
    else {
        return str;
    }
}
 
function fmtRawScore(entry, state) {
    var logEntry = 'Content set <abbr>raw score</abbr> to ' + $(entry).attr('value');
    if (state.currentActivity.score_min && state.currentActivity.score_max) {
       logEntry = logEntry + ' ( range is ' + state.currentActivity.score_min + ' .. ' +  state.currentActivity.score_max + ' )';
    }
    else if (state.currentActivity.score_min) {
       logEntry = logEntry + ' ( min is ' + state.currentActivity.score_min + ' )';
    }
    else if (state.currentActivity.score_max) {
       logEntry = logEntry + ' ( max is ' + state.currentActivity.score_max + ' )';
    }
    return logEntry;
}

// Turns any {<prop> = <value>} pairs at the start of the string into a dictionary object 
// returns propertyMap, part of str containing properties, and the remaining response
function splitOffProperties(str) {
   function helper(m, s, p) {
       // match { <prop> = <val> } ......
       var aMatch = s.match(/^(\s*\{\s*(\S+)\s*\=\s*(\S+)\s*\})(.*)$/);
       if (aMatch) {
          m[aMatch[2]] = aMatch[3];
          return helper(m, aMatch[4], p + aMatch[1]);
       }
       else {
           return {propMap : m,
                   propStr : p, 
                   remResp : s};
       }
   }
   return helper({}, str, '');
}

// returns an object with language elements to be inserted into interaction report:
// "Learner responded " + responseStr + " which " + correctnessStr + [correctStr]
// correctStr will be null if the response was correct.
function mkFmtResponse(interaction) {
    // used to format responses and response patterns
    
    function fmtOptNumeric (str) {
        var parts = str.split('[:]');
        if (parts.length == 1) {
           return str;
        }
        if ($.trim(parts[0]) == '') {
           return 'less than ' + parts[1];
        }
        if ($.trim(parts[1]) == '') {
           return 'greater than ' + parts[0];
        }
        return 'between ' + parts[0] + ' and ' + parts[1];
    }
    function mkFmtResp(resp) {
        var myResp = (resp ? resp : this);        
        if (!myResp) {
            myResp = '';
        }
        var props = splitOffProperties(myResp);
        myResp = props.remResp;
        var properties = $.trim(props.propStr);
        var propMap = props.propMap;
        
        if (myResp.length == 0) {
            return 'no choice'
        }
        if (interaction.type == SCORM_NUMERIC) {
               return fmtOptNumeric(myResp);;
        }        
        
        // For PERFORMANCE replace all the <step>[.]<answer> clauses
        if (interaction.type == SCORM_PERFORMANCE) {
            var steps  = $(myResp.split('[,]')).map(function () {
                var partparts = this.split('[.]');
                var res = '';
                if ($.trim(partparts[0]) !== '') {
                    res = 'at <abbr>step</abbr> <span class="launch_int_step">' + $.trim(partparts[0]) + '</span> ';
                }
                if ($.trim(partparts[1]) !== '') {
                    res = res + fmtOptNumeric($.trim(partparts[1]));
                }
                return res;
            });          
            myResp = steps.get().join('[,]');
        }
        
        if (interaction.type == SCORM_SEQUENCING ||
            (interaction.type == SCORM_PERFORMANCE && 
               (propMap.order_matters == null || propMap.order_matters))) {  //order_matters defaults to true  
            myResp = myResp.split('[,]').join(' then ');
        }
        else {
            myResp = $.map(myResp.split('[,]'),
                           function(alt) {
                             return (interaction.type == SCORM_CHOICE ? '<abbr>option with id</abbr> ' + alt : alt);
                           })
                         .join(' and ');
        }
        myResp = myResp.split('[.]').join(' matches ');
        return (properties != '' ? '<span class="launch_string_properties">' + properties + '</span> ' : '' ) + myResp;
   }
   var isCorrect = interaction._resps[0].result == 'correct';
   var result = {responseStr: (interaction.type == SCORM_CHOICE ? 'selected ' : 'responded ') +  '<span class="launch_int_response">' + mkFmtResp(interaction._resps[0].learner_response), 
                 // isNaN is an 'interesting' way to see if a string is numeric or not
                 correctnessStr:(isNaN(interaction._resps[0].result) ? 
                                    'is <span class="' + (isCorrect ? 'launch_int_response_correct' : 'launch_int_response_incorrect') + '">' + mkAbbr(interaction._resps[0].result) + '</span>' : 
                                    'has a <abbr>correctness value</abbr> of <span class="launch_int_response_correct_numeric">' + interaction._resps[0].result + '</span>')
                };
   if (!isCorrect && 
       interaction.type !== SCORM_TRUE_FALSE && // Don't need to know the correct response if true/false question is incorrect ...
       interaction.correct_responses && interaction.correct_responses.length > 0) {
       var formattedResponses = $(interaction.correct_responses).map(mkFmtResp);
       // <alt1>, or <alt2>, or <alt3> ....
       result.correctStr = ', the correct answer' + (formattedResponses.length > 1 ? 's are ' : ' is ') + '<span class="launch_int_correct_pattern">' + 
                                      (formattedResponses.length > 1 ? formattedResponses.slice(0,formattedResponses.length-2).join(', ') + ', or' : '') +
                                      formattedResponses[formattedResponses.length-1] +
                                      '</span>';
   }
   return result;
}

// These objects map terse messages in the event log into proper language that we can insert into the report
fmtObjStatus = {success_passed:'<abbr>passed</abbr>', success_failed:'<abbr>failed</abbr>', 
                completion_completed:'<abbr>completed</abbr>', completion_incomplete:'<abbr>incomplete</abbr>'};

// Reached end of activity (or abrupt end of log, or new start activity ...)
// Emit activity markup
function emitActivity(entry, state)
{     
    if (state.currentActivity.isActive) {
        // We build an <li /> for the matching activity start containing all that activity's entries
        var startActivity = parseTimeStamp(state.currentActivity.start_ts);
        var endActivity = parseTimeStamp($(entry).attr('timestamp'));
        var durationMS = endActivity.getTime() - startActivity.getTime();
        // append this activity's entries to the log
        state.currentActivity.isActive = false;
        state.report =  state.report + 
                   '<div class="activity_items"><div class="hide_show_control"><div class="act_list_prefix">+</div><div class="launch_report_list_entry">' + emitTimestamp(state.currentActivity.start_ts, true) + 
                      '<div class="launch_report_list_entry_text">Entering activity <span class="launch_activity_title">' + state.currentActivity.title + '</span>, viewed for ' + fmtDuration(durationMS) + 
                   ".</div>" +
                   '<div class="launch_report_list_entry_text_invisible">Entering activity <span class="launch_activity_title">' + state.currentActivity.title + '</span>, viewed for ' + fmtDuration(durationMS) + 
                   ".</div></div></div>\n" +
                   "<div class='hide_show_div launch_activity_block'>\n";
        $.each(state.currentActivity.entries, function () {
                       processRtEntry(this,state);
        });
        state.report =  state.report + 
                   "</div></div>\n";
        state.currentActivity = mkEmptyActivityState();
    }
}

// This cycles through all the records within an activity and  
// remembers information (such as score_min, score_max) that we
// will need when we later emit the activity's log
function buildActivityState(entry, state)
{
     var event_parts = ($(entry).attr('event') ? $(entry).attr('event').split(' ') : ['no event']);
     
     switch (event_parts[0].toLowerCase())
     {
     case 'loadsco':
        // eat this
        break;
     case 'set':
        // match on the key up to first white space char
        var key_parts = $(entry).attr('key').split(' ');
        switch (key_parts[0].toLowerCase())
        {
        case 'score.min':
            state.currentActivity.score_min = $(entry).attr('value');
            break;
        case 'score.max':
            state.currentActivity.score_max = $(entry).attr('value');
            break;
        case "interactions":
            // These indicate a user attempt. If we get repeated ones then we push a new attempt
            var responseIndicators = {learner_response:1, timestamp:1, result:1, latency:1};
             
           // we build an array of interactions objects
            var primaryIndex = $(entry).attr('index');
            var myValue = ($(entry).attr('value') ? $(entry).attr('value') : $(entry).attr('valueHundredths'));
            if (!state.currentActivity.interactions[primaryIndex]) {
                // First time we have seen this interaction number
                state.currentActivity.interactions[primaryIndex] = {_resps:[{}]};
            }
            var interaction = state.currentActivity.interactions[primaryIndex];
            if (key_parts.length == 2) {
               // all the members of responseIndicator live here
               if (responseIndicators[key_parts[1]]) {
                   // If we have already seen a learner_response, and we have already seen 
                   // this element then push another resp, we are moving on
                   // otherwise we modify our current _resp
                   if (interaction._resps[interaction._resps.length-1].learner_resp &&
                       interaction._resps[interaction._resps.length-1][key_parts[1]]) {
                        interaction._resps.push({});
                   }
                   interaction._resps[interaction._resps.length-1][key_parts[1]] = myValue;
               }
               else {
                   // We just use the last reported value for elements not in responseIndicators
                   interaction[key_parts[1]] = myValue;
               }
            }
            else {
               // We have a secondary too, first create array if needed
               if (!interaction[key_parts[1]]) {
                   interaction[key_parts[1]] = [];
               }
               interaction[key_parts[1]][$(entry).attr('secondaryIndex')] = myValue;
            }
            if ($(entry).attr('identifier')) {
                interaction.id = $(entry).attr('identifier');
            }
            if (key_parts[1] == 'learner_response') {
               state.currentActivity.entries.push(entry);
            }
            break;
        default:
            state.currentActivity.entries.push(entry);
        }
        break;
    case 'unloadsco':
        //its show time
        state.currentActivity.entries.push(entry);
        emitActivity(entry, state);
        break;
    default:
        state.currentActivity.entries.push(entry);
     }
}

// Records between LoadSco/UnLoadSco are first passed to buildActivityState()
// Once we hit the UnLoadSco entry we pass them back through this routine
// to emit them to the log
function processRtEntry(entry, state)
{
     var timestamp = $(entry).attr('timestamp');
     var event_parts = ($(entry).attr('event') ? $(entry).attr('event').split(' ') : ['no event']);
     
     state.lastEntry = entry;
     
     if (state.currentActivity.isActive) {
         buildActivityState(entry, state);
     }
     else {
         switch (event_parts[0].toLowerCase())
         {
         case 'loadsco':
            // Should be no-op,  but doesn't harm
            emitActivity(entry, state);

            state.currentActivity.isActive = true;
            state.currentActivity.start_ts = timestamp;
            state.currentActivity.item_identifier = $(entry).attr('itemIdentifier');
            state.currentActivity.title = $(entry).attr('title');
            break;
         case 'unloadsco':
            // If this was requested by the course then tell the world, else it was covered by the user 
            // hitting a button in the gui and we have already indicated that
            if (state.pending_navrequest) {
                switch (state.pending_navrequest.request.toLowerCase()) {
                case "choice":
                    state.report = state.report + wrapLogEntry(entry, 'Content requested to go to <span class="launch_activity_title">' + state.pending_navrequest.request.target + '</span>.');
                    break;
                case "suspendall":
                    state.report = state.report + wrapLogEntry(entry, 'Content requested to <abbr>suspend all and exit</abbr>.');
                    break;
                case "exitall":
                    state.report = state.report + wrapLogEntry(entry, 'Content requested to <abbr>exit all</abbr>.');
                    break;
                case "continue":
                    state.report = state.report + wrapLogEntry(entry, 'Content requested to continue to next <abbr>activity</abbr>.');
                    break;
                case "previous":
                    state.report = state.report + wrapLogEntry(entry, 'Content requested to go back to previous <abbr>activity</abbr>.');
                    break;
                default:
                    state.report = state.report + wrapLogEntry(entry, 'Content requested to ' + mkAbbr(state.pending_navrequest.request.toLowerCase()) + '.');
                    break;
                }
            }
            state.gui_action = null;
            state.pending_navrequest = null;
            break;
         case 'set':
            // match on the key up to first white space char
            var key_parts = $(entry).attr('key').split(' ');
            switch (key_parts[0].toLowerCase())
            {
            case "score.raw":
                state.report = state.report + wrapLogEntry(entry, fmtRawScore(entry, state) + '.'); 
                break;
            case "score.scaled":
                state.report = state.report + wrapLogEntry(entry, 'Content set <abbr>scaled score</abbr> to ' + (parseFloat($(entry).attr('value'))*100) + '%'); 
                break;
            case "cmi.exit":
                if ($(entry).attr('value') && $(entry).attr('value') !== '') {
                    state.report = state.report + wrapLogEntry(entry, 'Content set exit type to ' + mkAbbr($(entry).attr('value')) + '.'); 
                }
                break;
            case "interactions":
                // index attribute tells us which question this was
                var interaction = state.currentActivity.interactions[$(entry).attr('index')];
                // its a logic error if there is no resp, but just do nothing if so
                if (interaction._resps && interaction._resps.length > 0) {
                    var fmtResponseObj = mkFmtResponse(interaction);
                    var logEntry = 'Learner was asked <span class="launch_int_question">' + $.trim(interaction.description ? interaction.description : interaction.id) + '</span>' +
                                    (interaction._resps[0].timestamp ? ' at ' + interaction._resps[0].timestamp : '') + '.<span class="launch_learner_response">' +
                                       (interaction._resps[0].latency ? 'After ' + fmtDuration(interaction._resps[0].latency) + ' l' : ' L') + 'earner ' + 
                                       fmtResponseObj.responseStr + '</span>' +
                                       ' which ' + fmtResponseObj.correctnessStr +
                                       (fmtResponseObj.correctStr ? fmtResponseObj.correctStr : '') +
                                       '</span>.';                
                    state.report = state.report + wrapLogEntry(entry, logEntry);
                    // remove first resp
                    interaction._resps = interaction._resps.slice(1);
                } 
                break;
            case "objectives":
                var summary = key_parts[1] + '_' + $(entry).attr('value');
                if (fmtObjStatus[summary]) { 
                    var logEntry = 'Content set <abbr>objective</abbr> with id <span class="launch_objective_id">' + $(entry).attr('identifier') + '</span> to <span class="launch_int_setvalue">' + fmtObjStatus[summary] + '</span>.';
                    state.report = state.report + wrapLogEntry(entry, logEntry); 
                }
                break;
            case "nav.request":
                state.pending_navrequest = {request: $(entry).attr('value')};
                if ( state.pending_navrequest == 'choice') {
                    state.pending_navrequest.target = $(entry).attr('targetActivityTitle');
                }
                break;
            default:
                var fmtValue = '<span class="launch_int_setvalue">' + ($(entry).attr('value') ? mkAbbr($(entry).attr('value')) : fmtDuration($(entry).attr('valueHundredths') * 10)) + '</span>';
                var logEntry = 'Content set ' + $(entry).attr('key') + ' to ' + fmtValue + '.';
                state.report = state.report + wrapLogEntry(entry, logEntry);
                break;
            }
            break;
         case 'sequencerpicksactivity':
//            if (state.gui_action == null) {
//                var navigationType = $(entry).attr('navigationType').toLowerCase();
//                var logEntry = '';
//                if (navigationType == 'start') {
//                    logEntry = 'Course is launched';            
//                }
//                else if (navigationType == 'resume all') {
//                    logEntry = 'Course resumes at <span class="launch_activity_title">' + $(entry).attr('targetActivityTitle') + '</span>';            
//                }
//                else {
//                    logEntry = 'Course ';
//                    if ($(entry).attr('targetActivityTitle')) {         
//                        logEntry = logEntry + 'requested to go to <span class="launch_activity_title">' + $(entry).attr('targetActivityTitle') + '</span> ';
//                    }
//                    else {
//                        logEntry = logEntry + 'requested to "' + navigationType.toLowerCase() + '" ';
//                    }           
//                }
//                state.report = state.report + wrapLogEntry(entry, logEntry);
//            }
            // reset gui_action
            state.gui_action = null;
            state.pending_navrequest = null;
            break;
         case 'rollup':
            state.report = state.report + wrapLogEntry(entry, mkAbbr('Course ' + event_parts[1].toLowerCase() + ' status') + ' changed to <span class="launch_int_setvalue">' + mkAbbr($(entry).attr('value').toLowerCase()) + '</span>.');
            break;
         case "gui":
            switch (event_parts[1].toLowerCase()) {
            case "returntolms":
                state.report = state.report + wrapLogEntry(entry, 'Learner requested to ' + $(entry).attr('action').toLowerCase()+ '.');
                break;
            case "close":
                state.report = state.report + wrapLogEntry(entry, 'Learner requested to ' + $(entry).attr('action').toLowerCase() + '<abbr>activity</abbr>.');
                break;
            case "continue":
                state.report = state.report + wrapLogEntry(entry, 'Learner requested to continue to next <abbr>activity</abbr>.');
                break;
            case "previous":
                state.report = state.report + wrapLogEntry(entry, 'Learner requested to go back to previous <abbr>activity</abbr>.');
                break;
            case "choice":
                state.report = state.report + wrapLogEntry(entry, 'Learner requested to go to <span class="launch_activity_title">' + $(entry).attr('targetTitle') + '</span>.');
                break;
            }
            // Tells sequencer that we have already displayed navigation request
            // and knocks out any course set pending navrequest
            state.gui_action = event_parts[1].toLowerCase();
            state.pending_navrequest = null;
            break;
         case 'resetruntime':
            state.report = state.report + wrapLogEntry(entry, 'Resetting runtime state for <abbr>activity</abbr> <span class="launch_activity_title">' + $(entry).attr('title') + '</span>.', 'launch_report_sequencing_entry');
            break;
         case 'attemptstart':
            state.report = state.report + wrapLogEntry(entry, 'Starting attempt number ' + $(entry).attr('attemptNo') + ' for <abbr>activity</abbr> <span class="launch_activity_title">' + $(entry).attr('title') + '</span>.', 'launch_report_sequencing_entry');
            break;
         default:
            //state.report = state.report + '<div class="launch_todo">' + wrapLogEntry(entry, '(' + $(entry).attr('f') + ')') + '</div>';
            break;
         }
     }
}

// Called after the whole log has been processed to tidy up loose ends
// (log may not have terminated cleanly). Will finish off state.report
function cleanUp(state) {
    if (state.lastEntry !== null) {
        emitActivity(state.lastEntry, state);
    }
}

// Records between LoadSco/UnLoadSco are first passed to buildActivityState()
// Once we hit the UnLoadSco entry we pass them back through this routine
// to emit them to the log
function processStatus(entry, state, title, doAppend) {
    var summaryHtml = 
      '<div class="score_fields_title launch_hist_title_width">' + title + '</div>' +
      '<div class="score_fields launch_history_score_fields">' +
          '<span class="info_label">Complete:</span>' + $(entry).attr('completion_status') + '<br />' +
          '<span class="info_label">Success:</span>' + $(entry).attr('success_status') + '<br />' +
          '<span class="info_label">Score:</span>' + $(entry).attr('score') + '<br />' +
          '<span class="info_label">Total Time:</span>' + fmtDuration($(entry).attr('total_time_tracked')*10) + '<br />' +
          '</div>';
    state.report = (doAppend ?  state.report + summaryHtml : summaryHtml + state.report);
}

function mkEmptyActivityState() {
    return {isActive:false, 
            entries: [],   // List of entries to be emitted once this activity exits
            interactions:[],
            objectives:[]
            };
}

// use the power of ajax to fetch and render launch reports on demand (on user click)
function loadReport(reportDiv, reportId, target) {
    var launchId = $(target).attr('id').match(/^launch_(.*)$/)[1];
    var requestData = {'launchId':launchId, 
                             'action':'getLaunchInfoXml', 
                             'configuration':extConfigurationString};
    
    //Optional regid field (useful in hosted environment)
    var regId = $(target).attr('regid');
    if(regId){
        requestData['regId'] = regId;
    }
    
    $.get(
        reportsHelperUrl,
        requestData,
        function(data){
            render(data, target);
        });
}

function render(data, reportDiv) {
    // Create an initial, empty state
    emittedTimestamp = '';
    if ($(data).find('RuntimeLog').length > 0) {
        var state = {report:"", // generated html for report
                     currentActivity:mkEmptyActivityState(), // info about the activity being processed
                     // Version of ScormEngine that made this history xml, useful for future extensions
                     version:$(data).find('RuntimeLog').attr('version')[0],
                     lastEntry: null // Holds last entry processed, useful to cleanup if log ends abruptly 
                    };
        $(data).find('RuntimeEvent').each(function(idx){ processRtEntry(this, state) });
        cleanUp(state);
        if ($(data).find('LaunchInfo').length == 1) {
            if ($(data).find('LaunchInfo').attr('clean_termination') == 'false') {
                state.report = state.report + '<span class="launch_unclean_termination">Launch did not end cleanly</span>';
            }
        }
        // Wrap narrative in a hide/show                    
        state.report =  
                   '<div class="launch_report">' +
                      '<div class="hide_show_control"><div class="act_list_prefix">+</div>' +
                            '<div class="launch_report_list_entry">Detailed Activity report.</div>' +
                      '</div>\n' +
                      '<div class="hide_show_div launch_activity_block" style="display:none">\n' +
                          '<div id="launchHideShowSequencing" class="launch_hide_show_sequencing">[Hide Sequencing]</div>' +
                          state.report +
                      '</div></div>';
        
        if ($(data).find('RegistrationStatusOnEntry').length == 1) {
            $(data).find('RegistrationStatusOnEntry').each(function(idx){ processStatus(this, state, 'Registration Status at Launch Start', false) });
        }
        if ($(data).find('RegistrationStatusOnExit').length == 1) {
            $(data).find('RegistrationStatusOnExit').each(function(idx){ processStatus(this, state, 'Registration Status at Launch End', true) });
        }
    }
    else {
        state = {report:"No Launch Data recorded"};
    }
    state.report = '<div class="launch_wrap_launch_report">' + state.report + '</div>';
    // render generated report
    $('div#receiver', reportDiv).html(state.report)
          .find("div.hide_show_control").bind('click', function (event) {
                                              // if (this == event.target) {
                                                   $(this).parent().find('div.hide_show_div')
                                                      .toggle();
                                                   $(this).find('div.act_list_prefix').html(
                                                           // $(this).parent().find('div.hide_show_div').is(':hidden') ? '+' : '-');
                                                            $(this).next().is(':hidden') ? '+' : '-');
                                              // }
                                               return false;
                                               })
                                        .css('cursor','pointer') 
                                        ; //.click();
    // add glossary to rendered report
    $('abbr', reportDiv).attr('title','Click me for my definition!')
                        .addClass('launch_abbr')
                        .click(function (event) {
                            if (currentPopup) {
                                currentPopup.remove();
                            }
                            currentPopup = 
                              $('<div></div>') 
                              .css({ 
                                    position: 'absolute', 
                                    left: event.pageX, 
                                    top: event.pageY, 
                                    cursor: 'pointer', 
                                    display: 'none' 
                                }) 
                                // convert text to glossary key by lowering case and removing spaces 
                              .html(scormGlossary[this.innerHTML.toLowerCase().replace(/\s*/g, '')]) 
                              .addClass('launch_glossary_popup') 
                              .click(function(){            
                                $(this).fadeOut(1500,function(){currentPopup = null; $(this).remove();}); 
                                }) 
                              .appendTo('body')   
                              .fadeIn();          
                            });
    // Set a click handler on the hide/show sequencing button 
    $('div#launchHideShowSequencing', reportDiv)
           .bind('click', function(event) {
                               $('div.launch_report_sequencing_entry', reportDiv).toggle();
                               $(event.target).text($('div.launch_report_sequencing_entry', reportDiv).is(':hidden') ? '[Show Sequencing]' : '[Hide Sequencing]');
                               //this is a hack for IE7 which screws up the positioning.  fix idea from: http://www.positioniseverything.net/explorer/ienondisappearcontentbugPIE/index.htm
                               $('div.launch_timestamp', reportDiv).css('position','relative').css('position','absolute');
                          }); 
    // Remove the ajax click handler so that we don't refetch the data
    // The add a click event that will toggle the visibility of the detailed view 
    $('div.hide_show_div', reportDiv).unbind('click').bind('click', function(event) {
                                  $('div.launch_activity_list', $(this).parent()).toggle();
                                  $(this).find('td.launch_listPrefix').html(
                                    $(this).parent().find('div.launch_activity_list').is(':hidden') ? '+' : '-');
                               }  ); 
}

// turn a time in milliseconds into a string that is easy on the eye
function fmtDuration(durMS) {

   var seconds = Math.floor(durMS / 1000);
   if (seconds < 60) {
       return seconds + " second" + (seconds == 1 ? "" : "s");
   }
   var hours = Math.floor(seconds / (60 * 60));
   minutes = Math.floor((seconds % (60 * 60)) / 60);
   return (hours > 0 ? hours + "hour" + (hours == 1 ? "" : "s") + ", " : "") +
          minutes + " minute" + (minutes == 1 ? "" : "s");
   
}

function parseTimeStamp (timeString, dt) {
  if (timeString == '') return null;
  // we don't care about the date, we are just going to set the time component
  var d = new Date('January 1, 1970'); 
  var time = timeString.match(/(\d+)(?::(\d\d))?(?::(\d\d))?(?:\.(\d*))?/);
  d.setHours( parseInt(time[1],10) );
  d.setMinutes( parseInt(time[2],10) || 0 );
  d.setSeconds(parseInt(time[3],10) || 0);
  d.setMilliseconds(parseInt(time[4],10) || 0);
  return d;
}

