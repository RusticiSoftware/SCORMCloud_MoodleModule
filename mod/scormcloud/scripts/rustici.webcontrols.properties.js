    var Properties;
    var LearningStandard;
    
    function dostuff()
    {
        //wait until the document is loaded...
        $(function() {
        $("#Message").html("Loading Properties...");
              $.ajax({
                type: "POST",
                url: "http://localhost/ScormEngineVS/ScormEngineInterface/ScormEngineService.asmx/GetPackagePropertiesXml",
                dataType: "xml",
                data: "externalPkgId=courseid|1003&externalConfig=",
                //contentType: "text/xml",
                success: function(xml){
                
                    if($.browser.msie)
                    {
                        Properties = xml.childNodes[1]; //ie
                    }
                    else
                    {
                        Properties = xml.firstChild;//firefox
                    }
                    
                    BuildPropertiesForm();
                    
                    GetPackageTitle();
                    GetLearningStandard();
                }//close success
              });
              
              
              
          });
      }
      
      function GetPackageTitle()
      {
        $.ajax({
                type: "POST",
                url: "http://localhost/ScormEngineVS/ScormEngineInterface/ScormEngineService.asmx/GetPackageTitle",
                dataType: "xml",
                data: "externalPkgId=courseid|1003&externalConfig=",
                //contentType: "text/xml",
                success: function(xml){
                
                    if($.browser.msie)
                    {
                        $("#PackageTitle").html(xml.childNodes[1]); //ie
                    }
                    else
                    {
                        $("#PackageTitle").html(xml.firstChild);//firefox
                    }
                    
                }//close success
              });
      }
      
      function GetLearningStandard()
      {
            $.ajax({
                type: "POST",
                url: "http://localhost/ScormEngineVS/ScormEngineInterface/ScormEngineService.asmx/GetPackageLearningStandard",
                dataType: "xml",
                data: "externalPkgId=courseid|1003&externalConfig=",
                //contentType: "text/xml",
                success: function(xml){
                    //alert(xml.documentElement.firstChild.nodeValue);
                    if($.browser.msie)
                    {
                        LearningStandard = xml.documentElement.firstChild.nodeValue;//xml.childNodes[1]; //ie
                    }
                    else
                    {
                        LearningStandard = xml.documentElement.firstChild.nodeValue;//firefox
                    }
                    //alert(typeof(LearningStandard));
                    
                    $("#LearningStandardText").html(LearningStandard);
                    
                    if($("#LearningStandard").html().indexOf("2004")>-1)
                    {
                        //this is a SCORM 2004 course - show the edit options.
                        $("#EditLearningStandard").show();//add edit button
                        $("#LearningStandardUpdate").hide();
                    }else{
                        $("#EditLearningStandard").hide();
                        $("#LearningStandardUpdate").hide();
                    }
                    
                }//close success
              });
      }
      
      function PopulateForm()
      {
        $(Properties).find("controls").each(function(){
                    
            $("#ckbShowNavBar").attr("checked",ConvertYesNo($(this).find('showNavBar').text()));
            $("#ckbShowFinishButton").attr("checked",ConvertYesNo($(this).find('showFinishButton').text()));
            $("#ckbShowCloseItem").attr("checked",ConvertYesNo($(this).find('showCloseItem').text()));
            $("#ckbShowProgressBar").attr("checked",ConvertYesNo($(this).find('showProgressBar').text()));
            $("#ckbShowHelp").attr("checked",ConvertYesNo($(this).find('showHelp').text()));
            //$("#").attr("checked",ConvertYesNo($(this).find('showTitleBar').text()));
            $("#ckbCourseStructureStartsOpen").attr("checked",ConvertYesNo($(this).find('courseStructureStartsOpen').text()));
            //$("#").attr("checked",ConvertYesNo($(this).find('enableFlowNav').text()));
            //$("#").attr("checked",ConvertYesNo($(this).find('enableChoiceNav').text()));
            
            $("#ddlStatusDisplayPreference").val($(this).find('statusDisplay').text());
            
            $("#ckbShowCourseStructure").attr("checked",ConvertYesNo($(this).find('showCourseStructure').text()));
            
        });

        $(Properties).find("appearence").each(function(){
            $("#txtCourseStructureWidth").attr("value",$(this).find('courseStructureWidth').text());
            
            if($(this).find('displayStage').children('required').children('width').text()==0)//there is no required width TODO: Is this a good enough check??
            {
                $("#ckbRequiredDems").attr("checked",false);
                $("#txtWidthForContent").val($(this).find('displayStage').children('desired').children('width').text());
                $("#txtHeightForContent").val($(this).find('displayStage').children('desired').children('height').text());
            }else{
                $("#ckbRequiredDems").attr("checked",true);
                $("#txtWidthForContent").val($(this).find('displayStage').children('required').children('width').text());
                $("#txtHeightForContent").val($(this).find('displayStage').children('required').children('height').text());
            }
            
            //Check for fullscreen
            if(ConvertYesNo($(this).find('displayStage').children('desired').children('fullscreen').text()) || ConvertYesNo($(this).find('displayStage').children('required').children('fullscreen').text()))
            {
                $("#rdoFullScreen").attr("checked",true);
                $("#txtWidthForContent").attr("disabled",true);
                $("#txtHeightForContent").attr("disabled",true);
            }else{
                $("#rdoFullScreen").attr("checked",false);
                if($(this).find('displayStage').children('desired').children('width').text()=="0" && $(this).find('displayStage').children('required').children('width').text()=="0")
                {
                  //not fullscreen and no desired or required width set - must be User Defined
                    $("#rdoUserValueDefaults").attr("checked",true);
                    $("#rdoSpecifyNewWindowDems").attr("checked",false);
                    $("#txtWidthForContent").attr("disabled",true);
                    $("#txtHeightForContent").attr("disabled",true);
                }else{
                    $("#rdoUserValueDefaults").attr("checked",false);
                    $("#rdoSpecifyNewWindowDems").attr("checked",true);
                    $("#txtWidthForContent").attr("disabled",false);
                    $("#txtHeightForContent").attr("disabled",false);
                }
                
            }
        });
        
        
        $(Properties).find("behavior").each(function(){
            $("#ckbPreventRightClick").attr("checked",ConvertYesNo($(this).find('disableRightClick').text()));
            $("#ckbPreventWindowResize").attr("checked",ConvertYesNo($(this).find('preventWindowResize').text()));
            $("#ddlInvalidMenuItemAction").val($(this).find('invalidMenuItemAction').text());
            $("#ddlScoLaunchType").val($(this).find("launch").children("sco").text());
            $("#ddlPlayerLaunchType").val($(this).find("launch").children("player").text());
            $("#ckbFinishCausesImmediateCommit").attr("checked",ConvertYesNo($(this).find("finishCausesImmediateCommit").text()));
            $("#ckbLogoutCausesPlayerExit").attr("checked",ConvertYesNo($(this).find("logoutCausesPlayerExit").text()));
            $("#ckbWrapSCOWindowWithAPI").attr("checked",ConvertYesNo($(this).find("launch").children("wrapScoWindowWithApi").text()));
            $("#ckbAlwaysFlowToFirstSCO").attr("checked",ConvertYesNo($(this).find("alwaysFlowToFirstSco").text()));
            $("#ckbRawScoreCanActAsScaledScore").attr("checked",ConvertYesNo($(this).find("scaleRawScore").text()));
            $("#ckbRollupEmptySetToUnknownLookAhead").attr("checked",ConvertYesNo($(this).find("rollupEmptySetToUnknown").text()));
            //Sequencer Mode
            $("#ddlSequencerMode").val($(this).find("lookaheadSequencerMode").text());
            //Reset Runtime Data Timing
            $("#ddlResetRunTimeDataTiming").val($(this).find("resetRtTiming").text());
            //SCORM 2004 Edition
            
            //MaxFailedAttempts
            $("#txtMaximumFailedAttempts").val($(this).find("communications").children("maxFailedSubmissions").text());
            //Commit Frequency
            $("#txtCommitFrequency").val($(this).find("communications").children("commitFrequency").text());
        
        
        //Debugger Options
        
            $("#rdoControlOff").attr("checked",true );//set this one by default
            
            if($(this).find('controlAudit').text()=="true")
            {
                $("#rdoControlAudit").attr("checked",true );
            }
            if($(this).find('controlDetailed').text()=="true")
            {
                $("#rdoControlDetailed").attr("checked",true );
            }
            
            $("#rdoRuntimeOff").attr("checked",true );//set this one by default
            
            if($(this).find('runtimeAudit').text()=="true")
            {
                $("#rdoRuntimeAudit").attr("checked",true );
            }
            if($(this).find('runtimeDetailed').text()=="true")
            {
                $("#rdoRuntimeDetailed").attr("checked",true );
            }
            
            $("#rdoSequencingOff").attr("checked",true );//set this one by default
            
            if($(this).find('sequencingAudit').text()=="true")
            {
                $("#rdoSequencingAudit").attr("checked",true );
            }
            if($(this).find('sequencingDetailed').text()=="true")
            {
                $("#rdoSequencingDetailed").attr("checked",true );
            }
            
            $("#rdoLookOff").attr("checked",true );//set this one by default
            
            if($(this).find('lookaheadAudit').text()=="true")
            {
                $("#rdoLookAudit").attr("checked",true );
            }
            if($(this).find('lookaheadDetailed').text()=="true")
            {
                $("#rdoLookDetailed").attr("checked",true );
            }
           if($(this).find("includeTimestamps").text()=='true')
           {
                $("#ckbIncludeTimestamps").attr("checked",true);
           }else{
                $("#ckbIncludeTimestamps").attr("checked",false);
           }
       
       
       //Exit Actions
       //alert($(this).find("exitActions").children("intermediateSco").children("satisfied").children("normal").text());
            $("#ddlIntNormalSatisfied").val($(this).find("exitActions").children("intermediateSco").children("satisfied").children("normal").text());
            $("#ddlIntNormalNotSatisfied").val($(this).find("exitActions").children("intermediateSco").children("notSatisfied").children("normal").text());
            $("#ddlIntSuspendSatisfied").val($(this).find("exitActions").children("intermediateSco").children("satisfied").children("suspend").text());
            $("#ddlIntSuspendNotSatisfied").val($(this).find("exitActions").children("intermediateSco").children("notSatisfied").children("suspend").text());
            $("#ddlIntTimeoutSatisfied").val($(this).find("exitActions").children("intermediateSco").children("satisfied").children("timeout").text());
            $("#ddlIntTimeoutNotSatisfied").val($(this).find("exitActions").children("intermediateSco").children("notSatisfied").children("timeout").text());
            $("#ddlIntLogoutSatisfied").val($(this).find("exitActions").children("intermediateSco").children("satisfied").children("logout").text());
            $("#ddlIntLogoutNotSatisfied").val($(this).find("exitActions").children("intermediateSco").children("notSatisfied").children("logout").text());

            $("#ddlFinalNormalSatisfied").val($(this).find("exitActions").children("finalSco").children("satisfied").children("normal").text());
            $("#ddlFinalNormalNotSatisfied").val($(this).find("exitActions").children("finalSco").children("notSatisfied").children("normal").text());
            $("#ddlFinalSuspendSatisfied").val($(this).find("exitActions").children("finalSco").children("satisfied").children("suspend").text());
            $("#ddlFinalSuspendNotSatisfied").val($(this).find("exitActions").children("finalSco").children("notSatisfied").children("suspend").text());
            $("#ddlFinalTimeoutSatisfied").val($(this).find("exitActions").children("finalSco").children("satisfied").children("timeout").text());
            $("#ddlFinalTimeoutNotSatisfied").val($(this).find("exitActions").children("finalSco").children("notSatisfied").children("timeout").text());
            $("#ddlFinalLogoutSatisfied").val($(this).find("exitActions").children("finalSco").children("satisfied").children("logout").text());
            $("#ddlFinalLogoutNotSatisfied").val($(this).find("exitActions").children("finalSco").children("notSatisfied").children("logout").text());
   
            $("#ddlScoreRollupMode").val($(this).find("scoreRollupMode").text());  
            //Number of scoring objects  
            $("#ddlStatusRollupMode").val($(this).find("statusRollupMode").text()); 
            //Threshold score for completion
            $("#ckbFirstScoIsPretest").attr("checked",$(this).find("firstScoIsPretest").text());
       
       
        });//end behavior each
      }
      
      function SetupClickEvents()
      {
          $("#rdoUserValueDefaults").click(function(){
            $("#txtWidthForContent").attr("disabled",true);
            $("#txtHeightForContent").attr("disabled",true);
            $("#ckbRequiredDems").attr("disabled",true);
          });
          
          $("#rdoFullScreen").click(function(){
            $("#txtWidthForContent").attr("disabled",true);
            $("#txtHeightForContent").attr("disabled",true);
            $("#ckbRequiredDems").attr("disabled",false);
          });
          
          $("#rdoSpecifyNewWindowDems").click(function(){
            $("#txtWidthForContent").attr("disabled",false);
            $("#txtHeightForContent").attr("disabled",false);
            $("#ckbRequiredDems").attr("disabled",false);
          });
          
          
          $("#EditLearningStandard").click(function(){
              $("#LearningStandardUpdate").show();
              $("#EditLearningStandard").hide();
              $("#LearningStandardText").hide();
          });
          
          $("#SaveLearningStandard").click(function(){
                UpdatePackageLearningStandard($("#ddlScormEdition").val());
                $("#LearningStandardText").html($("#ddlScormEdition").val());
                $("#EditLearningStandard").show();
                $("#LearningStandardText").show();
                $("#LearningStandardUpdate").hide();
            });
            
            $("#CancelLearningStandard").click(function()
            {
                $("#LearningStandardUpdate").hide();
                $("#EditLearningStandard").show();
                $("#LearningStandardText").show();
            });
          
          
          $("#btnSave").click(function(){
            $("#Message").html("Saving Changes...");
            
            //Set Properties for controls
            UpdatePropertiesFromCurrentSettings();
            
            //now build the string from the xml and send it back...
            var string = (new XMLSerializer()).serializeToString(Properties);
            SetPropertyXml(string);
            //alert(string);
          });
          
          
            
      }
      
      function UpdatePropertiesFromCurrentSettings()
      {
        //Show Nav Bar
        if($("#ckbShowNavBar:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('showNavBar').text('no') 
        }else if($("#ckbShowNavBar:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('showNavBar').text('yes')
        }

        //Show Finish Button
        if($("#ckbShowFinishButton:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('showFinishButton').text('no') 
        }else if($("#ckbShowFinishButton:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('showFinishButton').text('yes')
        }

        //Show Close Item
        if($("#ckbShowCloseItem:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('showCloseItem').text('no') 
        }else if($("#ckbShowCloseItem:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('showCloseItem').text('yes')
        }

        //Show Progress Bar
        if($("#ckbShowProgressBar:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('showProgressBar').text('no') 
        }else if($("#ckbShowProgressBar:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('showProgressBar').text('yes')
        }

        //Show Help
        if($("#ckbShowHelp:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('showHelp').text('no') 
        }else if($("#ckbShowHelp:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('showHelp').text('yes')
        }

        //Prevent Right Click
        if($("#ckbPreventRightClick:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('disableRightClick').text('no') 
        }else if($("#ckbPreventRightClick:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('disableRightClick').text('yes')
        }

        //Course Structure Starts Open
        if($("#ckbCourseStructureStartsOpen:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('courseStructureStartsOpen').text('no') 
        }else if($("#ckbCourseStructureStartsOpen:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('courseStructureStartsOpen').text('yes')
        }

        //Course Structure Starts Open
        if($("#ckbShowCourseStructure:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('showCourseStructure').text('no') 
        }else if($("#ckbShowCourseStructure:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('showCourseStructure').text('yes')
        }

        $(Properties).find('courseStructureWidth').text($('#txtCourseStructureWidth').val());
        $(Properties).find('statusDisplay').text($('#ddlStatusDisplayPreference').val());
        $(Properties).find('invalidMenuItemAction').text($("#ddlInvalidMenuItemAction").val());


        //Set Properties for Launch Behavior

        $(Properties).find('launch').children('sco').text($("#ddlScoLaunchType").val());
        $(Properties).find('launch').children('player').text($("#ddlPlayerLaunchType").val());

        //User Value Defaults
        if($("#rdoUserValueDefaults:checked").val()==undefined)
        {   //it's been unchecked
             
        }else if($("#rdoUserValueDefaults:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find("appearence").children("displayStage").children("desired").children("fullscreen").text('no'); 
            $(Properties).find("appearence").children("displayStage").children("required").children("fullscreen").text('no');
        }

        //Full Screen
        if($("#rdoFullScreen:checked").val()==undefined)
        {   //it's been unchecked
             $(Properties).find("appearence").children("displayStage").children("desired").children("fullscreen").text('no');
             $(Properties).find("appearence").children("displayStage").children("required").children("fullscreen").text('no');
        }else if($("#rdoFullScreen:checked").val()=='on')
        {   //it's been checked 
            if($("#ckbRequiredDems:checked").val()=='on')
            {
                $(Properties).find("appearence").children("displayStage").children("required").children("fullscreen").text('yes');
                $(Properties).find("appearence").children("displayStage").children("desired").children("fullscreen").text('no');
            }else{
                $(Properties).find("appearence").children("displayStage").children("desired").children("fullscreen").text('yes'); 
                $(Properties).find("appearence").children("displayStage").children("required").children("fullscreen").text('no');
            }
        }


        //New Window Dems
        if($("#rdoSpecifyNewWindowDems:checked").val()==undefined)
        {   //it's been unchecked
             
        }else if($("#rdoSpecifyNewWindowDems:checked").val()=='on')
        {   //it's been checked 
            //remove fullscreen
            $(Properties).find("appearence").children("displayStage").children("desired").children("fullscreen").text('no');
            $(Properties).find("appearence").children("displayStage").children("required").children("fullscreen").text('no');
            //add the width and height where appropriate
            if($("#ckbRequiredDems").val()=='on')
            {
                $(Properties).find("appearence").children("displayStage").children("required").children("width").text($("#txtWidthForContent").val());
                $(Properties).find("appearence").children("displayStage").children("required").children("height").text($("#txtHeightForContent").val());
                //set the others to zero
                $(Properties).find("appearence").children("displayStage").children("desired").children("width").text("0");
                $(Properties).find("appearence").children("displayStage").children("desired").children("height").text("0");
            }else{
                $(Properties).find("appearence").children("displayStage").children("desired").children("width").text($("#txtWidthForContent").val());
                $(Properties).find("appearence").children("displayStage").children("desired").children("height").text($("#txtHeightForContent").val());
                //set the others to zero
                $(Properties).find("appearence").children("displayStage").children("required").children("width").text("0");
                $(Properties).find("appearence").children("displayStage").children("required").children("height").text("0");
            }
        }

        //Prevent Window Resize
        if($("#ckbPreventWindowResize:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('preventWindowResize').text('no') 
        }else if($("#ckbPreventWindowResize:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('preventWindowResize').text('yes')
        }

        //finishCausesImmediateCommit
        if($("#ckbFinishCausesImmediateCommit:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('finishCausesImmediateCommit').text('no') 
        }else if($("#ckbFinishCausesImmediateCommit:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('finishCausesImmediateCommit').text('yes')
        }
        //logoutCausesPlayerExit
        if($("#ckbLogoutCausesPlayerExit:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('logoutCausesPlayerExit').text('no') 
        }else if($("#ckbLogoutCausesPlayerExit:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('logoutCausesPlayerExit').text('yes')
        }
        //wrapScoWindowWithApi
        if($("#ckbWrapSCOWindowWithAPI:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('wrapScoWindowWithApi').text('no') 
        }else if($("#ckbWrapSCOWindowWithAPI:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('wrapScoWindowWithApi').text('yes')
        }
        //alwaysFlowToFirstSco
        if($("#ckbAlwaysFlowToFirstSCO:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('alwaysFlowToFirstSco').text('no') 
        }else if($("#ckbAlwaysFlowToFirstSCO:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('alwaysFlowToFirstSco').text('yes')
        }
        //scaleRawScore
        if($("#ckbRawScoreCanActAsScaledScore:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('scaleRawScore').text('no') 
        }else if($("#ckbRawScoreCanActAsScaledScore:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('scaleRawScore').text('yes')
        }
        //rollupEmptySetToUnknown
        if($("#ckbRollupEmptySetToUnknownLookAhead:checked").val()==undefined)
        {   //it's been unchecked
            $(Properties).find('rollupEmptySetToUnknown').text('no') 
        }else if($("#ckbRollupEmptySetToUnknownLookAhead:checked").val()=='on')
        {   //it's been checked 
            $(Properties).find('rollupEmptySetToUnknown').text('yes')
        }

        //Sequencer Mode
        $(Properties).find("lookaheadSequencerMode").text($("#ddlSequencerMode").val());
        //Reset Runtime Data Timing
        $(Properties).find("resetRtTiming").text($("#ddlResetRunTimeDataTiming").val());
        //SCORM 2004 Edition

        //MaxFailedAttempts
        $(Properties).find("maxFailedSubmissions").text($("#txtMaximumFailedAttempts").val());
        //Commit Frequency
        $(Properties).find("commitFrequency").text($("#txtCommitFrequency").val());


        //Debugger Options
            if($("#rdoControlOff:checked").val()=='on')
            {
                $(Properties).find('controlAudit').text("false");
                $(Properties).find('controlDetailed').text("false");
            }else{
                if($("#rdoControlAudit:checked").val()=='on')
                {
                    $(Properties).find('controlAudit').text("true");
                    $(Properties).find('controlDetailed').text("false");
                }else{
                    $(Properties).find('controlAudit').text("false");
                    $(Properties).find('controlDetailed').text("true");
                }
            }
            
            if($("#rdoRuntimeOff:checked").val()=='on')
            {
                $(Properties).find('runtimeAudit').text("false");
                $(Properties).find('runtimeDetailed').text("false");
            }else{
                if($("#rdoRuntimeAudit:checked").val()=='on')
                {
                    $(Properties).find('runtimeAudit').text("true");
                    $(Properties).find('runtimeDetailed').text("false");
                }else{
                    $(Properties).find('runtimeAudit').text("false");
                    $(Properties).find('runtimeDetailed').text("true");
                }
            }
            
            if($("#rdoSequencingOff:checked").val()=='on')
            {
                $(Properties).find('sequencingAudit').text("false");
                $(Properties).find('sequencingDetailed').text("false");
            }else{
                if($("#rdoSequencingAudit:checked").val()=='on')
                {
                    $(Properties).find('sequencingAudit').text("true");
                    $(Properties).find('sequencingDetailed').text("false");
                }else{
                    $(Properties).find('sequencingAudit').text("false");
                    $(Properties).find('sequencingDetailed').text("true");
                }
            }
               
            if($("#rdoLookOff:checked").val()=='on')
            {
                $(Properties).find('lookaheadAudit').text("false");
                $(Properties).find('lookaheadDetailed').text("false");
            }else{
                if($("#rdoLookAudit:checked").val()=='on')
                {
                    $(Properties).find('lookaheadAudit').text("true");
                    $(Properties).find('lookaheadDetailed').text("false");
                }else{
                    $(Properties).find('lookaheadAudit').text("false");
                    $(Properties).find('lookaheadDetailed').text("true");
                }
            }    
            

            if($("#ckbIncludeTimestamps:checked").val()==undefined)
            {   //it's been unchecked
                $(Properties).find('debug').children('includeTimestamps').text('false') 
            }else if($("#ckbIncludeTimestamps:checked").val()=='on')
            {   //it's been checked 
                $(Properties).find('debug').children('includeTimestamps').text('true')
            }
            
            
            $(Properties).find('exitActions').children('intermediateSco').children('satisfied').children('normal').text($('#ddlIntNormalSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('notSatisfied').children('normal').text($('#ddlIntNormalNotSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('satisfied').children('suspend').text($('#ddlIntSuspendSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('notSatisfied').children('suspend').text($('#ddlIntSuspendNotSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('satisfied').children('timeout').text($('#ddlIntTimeoutSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('notSatisfied').children('timeout').text($('#ddlIntTimeoutNotSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('satisfied').children('logout').text($('#ddlIntLogoutSatisfied').val());
            $(Properties).find('exitActions').children('intermediateSco').children('notSatisfied').children('logout').text($('#ddlIntLogoutNotSatisfied').val());

            $(Properties).find('exitActions').children('finalSco').children('satisfied').children('normal').text($('#ddlFinalNormalSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('notSatisfied').children('normal').text($('#ddlFinalNormalNotSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('satisfied').children('suspend').text($('#ddlFinalSuspendSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('notSatisfied').children('suspend').text($('#ddlFinalSuspendNotSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('satisfied').children('timeout').text($('#ddlFinalTimeoutSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('notSatisfied').children('timeout').text($('#ddlFinalTimeoutNotSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('satisfied').children('logout').text($('#ddlFinalLogoutSatisfied').val());
            $(Properties).find('exitActions').children('finalSco').children('notSatisfied').children('logout').text($('#ddlFinalLogoutNotSatisfied').val());
            
      }
      
      /*********************************************
      ***           AJAX Functions
      **********************************************/
      
      function SetPropertyXml(propertyXml)
      {
            $("#Message").html("Saving Changes...");
            $.ajax({
                type: "POST",
                url: "http://localhost/ScormEngineVS/ScormEngineInterface/ScormEngineService.asmx/SetPackagePropertiesXml",
                dataType: "xml",
                data: "externalPkgId=courseid|1003&externalConfig=&propertyXml=" + propertyXml,
                //contentType: "application/json",
                success: function(msg){
                //alert(msg);
                    //var Properties = eval("(" + msg + ")");
                  $("#Message").html("Changes Saved : " + msg.documentElement.firstChild.nodeValue);
                    }
              });
      }
      
      function UpdatePackageLearningStandard(LearningStandardValue)
      {
            $("#Message").html("Saving Changes...");
            $.ajax({
                type: "POST",
                url: "http://localhost/ScormEngineVS/ScormEngineInterface/ScormEngineService.asmx/UpdatePackageLearningStandard",
                dataType: "xml",
                data: "externalPkgId=courseid|1003&externalConfig=&NewLearningStandardString=" + LearningStandardValue,
                //contentType: "application/json",
                success: function(msg){
                //alert(msg);
                    //var Properties = eval("(" + msg + ")");
                  $("#Message").html("Changes Saved");
                    }
              });
      }
      
      
      
      /*************************************
      ***
      ***      HTML Writer Code
      ***
      **************************************/
      
      function BuildPropertiesForm()
      {
        var PropertiesControlHtml = BuildHeaderBar();
        PropertiesControlHtml += "<br /><br /><div style=\"font-size: x-small\"><div id=\"PropertyContainer\">";
        PropertiesControlHtml += BuildNavigationBar();
        PropertiesControlHtml += BuildNavigationalControls();
        PropertiesControlHtml += BuildLaunchBehavior();
        PropertiesControlHtml += BuildRudimentarySequencing();
        PropertiesControlHtml += BuildRudimentaryRollup();
        PropertiesControlHtml += BuildCompatibilitySettings();
        PropertiesControlHtml += BuildCommunicationSettings();
        PropertiesControlHtml += BuildDebuggerOptions();
        PropertiesControlHtml += BuildPresets();
        PropertiesControlHtml += BuildSaveBar();
        PropertiesControlHtml += "</div></div>";
        
        $("#PropertiesControl").html(PropertiesControlHtml);
      
        PopulateForm();
      
        $("#Message").html("Properties Loaded.");
        
        $("#PropertyContainer").tabs();
        
        //setup click events
        SetupClickEvents();
      
        
      }
      
      function BuildHeaderBar()
      {
        
        var HeaderHtml = "<div id=\"header\" style=\"height: 30px\">";
        HeaderHtml += "<div id=\"PackageTitle\" style=\"float: left;\"></div>";
        HeaderHtml += "<div id=\"LearningStandard\" style=\"float: right\">";
        HeaderHtml += "<div id=\"LearningStandardText\"></div>";
        HeaderHtml += "<a href=\"#\" id=\"EditLearningStandard\">edit</a>";
        HeaderHtml += "<div id=\"LearningStandardUpdate\">";
        HeaderHtml += "<select id=\"ddlScormEdition\">";
        HeaderHtml += "<option value=\"scorm20042ndedition\">SCORM 2004 2nd Edition</option>";
        HeaderHtml += "<option value=\"scorm2004\">SCORM 2004 3rd Edition</option>";
        HeaderHtml += "</select>";
        HeaderHtml += "&nbsp;&nbsp;<a id=\"SaveLearningStandard\" href=\"#\">save</a>&nbsp;&nbsp;&nbsp; <a id=\"CancelLearningStandard\" href=\"#\">cancel</a></div>";
        HeaderHtml += "</div></div>";
      
        return HeaderHtml;
      
      }
      
      function BuildNavigationBar()
      {
        var NavBar = "<ul>";
        NavBar += "<li><a href=\"#tabs-1\">Navigational Controls</a></li>";
        NavBar += "<li><a href=\"#tabs-2\">Launch Behavior</a></li>";
        NavBar += "<li><a href=\"#tabs-3\">Rudimentary Sequencing</a></li>";
        NavBar += "<li><a href=\"#tabs-4\">Rudimentary Rollup</a></li>";
        NavBar += "<li><a href=\"#tabs-5\">Compatibility Settings</a></li>";
        NavBar += "<li><a href=\"#tabs-6\">Communication Settings</a></li>";
        NavBar += "<li><a href=\"#tabs-7\">Debugger Options</a></li>";
        NavBar += "<li><a href=\"#tabs-8\">Presets</a></li>";
        NavBar += "</ul>";
        
        return NavBar;
      }
      
      function BuildNavigationalControls()
      {
        var NavControls = "<div id=\"tabs-1\">";
        NavControls += "<div id=\"TabDescription1\">";
        NavControls += "<h3>Navigational Controls</h3>These settings determine the availability of navigational controls in the SCORM Player.</div>";
        NavControls += "<br /><hr /><br />";
        NavControls += "<table width=\"100%\">";
        NavControls += "<tr><td width=\"50%\">";
        NavControls += "<input type=\"checkbox\" id=\"ckbShowNavBar\" />&nbsp;Show Navigation Bar";
        NavControls += "<br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" id=\"ckbShowFinishButton\" />&nbsp;Show Finish Button";
        NavControls += "<br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" id=\"ckbShowCloseItem\" />&nbsp;Show Close SCO Button";
        NavControls += "<br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" id=\"ckbShowProgressBar\" />&nbsp;Show Progress Bar";
        NavControls += "<br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" id=\"ckbShowHelp\" />&nbsp;Show Help";
        NavControls += "<br />";
        NavControls += "<br />";
        NavControls += "<input type=\"checkbox\" id=\"ckbPreventRightClick\" />&nbsp;Prevent Right Click";
        NavControls += "<br />";
        NavControls += "</td>";
        NavControls += "<td>";
        NavControls += "<input type=\"checkbox\" id=\"ckbShowCourseStructure\" />&nbsp;Show Course Structure";
        NavControls += "<br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" id=\"ckbCourseStructureStartsOpen\" />&nbsp;Course";
        NavControls += "Structure Starts Open";
        NavControls += "<br />";
        NavControls += "<br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Course Structure Width&nbsp;<input type=\"text\" id=\"txtCourseStructureWidth\" style=\"width: 50px\" />&nbsp;pixels";
        NavControls += "<br /><br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Structure Status Display&nbsp;";
        NavControls += "<select id=\"ddlStatusDisplayPreference\">";
        NavControls += "<option value=\"success only\">Success Only</option>";
        NavControls += "<option value=\"completion only\">Completion Only</option>";
        NavControls += "<option value=\"separate\">Separate</option>";
        NavControls += "<option value=\"combined\">Combined</option>";
        NavControls += "<option value=\"none\">None</option>";
        NavControls += "</select>";
        NavControls += "<br /><br />";
        NavControls += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Invalid Menu Item Action&nbsp;";
        NavControls += "<select id=\"ddlInvalidMenuItemAction\">";
        NavControls += "<option value=\"show\">Show and Enable Links</option>";
        NavControls += "<option value=\"hide\">Hide</option>";
        NavControls += "<option value=\"disable\">Show but Disable Links</option>";
        NavControls += "</select>";
        NavControls += "</td></tr></table></div>";
        return NavControls;
      }
      function BuildLaunchBehavior()
      {
        var LaunchBehavior = "<div id=\"tabs-2\">";
        LaunchBehavior += "<div id=\"TabDescription2\">";
        LaunchBehavior += "<h3>Launch Behavior</h3>";
        LaunchBehavior += "These settings determine how the parts of the SCORM Player will be launched.";
        LaunchBehavior += "</div>";
        LaunchBehavior += "<br /><hr /><br />";
        LaunchBehavior += "<table width=\"100%\"><tr><td>";
        LaunchBehavior += "SCO Launch Type";
        LaunchBehavior += "<select id=\"ddlScoLaunchType\">";
        LaunchBehavior += "<option value=\"frameset\">Frameset</option>";
        LaunchBehavior += "<option value=\"new window\">New Window</option>";
        LaunchBehavior += "<option value=\"new window, after click\">New Window After Click</option>";
        LaunchBehavior += "<option value=\"new window without browser toolbar\">New Window Without Browser Toolbar</option>";
        LaunchBehavior += "<option value=\"new window,after click,without browser toolbar\">New Window Without Browser Toolbar After Click</option>";
        LaunchBehavior += "</select>";
        LaunchBehavior += "<br />";
        LaunchBehavior += "<br />";
        LaunchBehavior += "Player Launch Type";
        LaunchBehavior += "<select id=\"ddlPlayerLaunchType\">";
        LaunchBehavior += "<option value=\"frameset\">Frameset</option>";
        LaunchBehavior += "<option value=\"new window\">New Window</option>";
        LaunchBehavior += "<option value=\"new window, after click\">New Window After Click</option>";
        LaunchBehavior += "<option value=\"new window without browser toolbar\">New Window Without Browser Toolbar</option>";
        LaunchBehavior += "<option value=\"new window,after click,without browser toolbar\">New Window Without Browser Toolbar After Click</option>";
        LaunchBehavior += "</select>";
        LaunchBehavior += "<br /><br />";
        LaunchBehavior += "<h4>New Window Options:</h4>";
        LaunchBehavior += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        LaunchBehavior += "<input name=\"NewWindowOptions\" type=\"radio\" id=\"rdoUserValueDefaults\" />User Value Defaults";
        LaunchBehavior += "<br />";
        LaunchBehavior += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        LaunchBehavior += "<input name=\"NewWindowOptions\" type=\"radio\" id=\"rdoFullScreen\" />Full Screen";
        LaunchBehavior += "<br />";
        LaunchBehavior += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        LaunchBehavior += "<input name=\"NewWindowOptions\" type=\"radio\" id=\"rdoSpecifyNewWindowDems\" />Specify New Window Dimensions";
        LaunchBehavior += "<br />";
        LaunchBehavior += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Width for content:&nbsp;";
        LaunchBehavior += "<input type=\"text\" id=\"txtWidthForContent\" />&nbsp;pixels";
        LaunchBehavior += "<br />";
        LaunchBehavior += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Height for content:&nbsp;<inputtype=\"text\" id=\"txtHeightForContent\" />&nbsp;pixels";
        LaunchBehavior += "<br />";
        LaunchBehavior += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        LaunchBehavior += "<input type=\"checkbox\" id=\"ckbRequiredDems\" />&nbsp;REQUIRED: Above dimensions are required for <br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the course to function properly";
        LaunchBehavior += "<br /><br />";
        LaunchBehavior += "<input type=\"checkbox\" id=\"ckbPreventWindowResize\" />&nbsp;Prevent Window Resize";
        LaunchBehavior += "<br /></td></tr></table></div>";
        return LaunchBehavior;
      }
      function BuildRudimentarySequencing()
      {
        var RudimentarySequencing = "<div id=\"tabs-3\">";
        RudimentarySequencing += "<div id=\"TabDescription3\">";
        RudimentarySequencing += "<h3>Rudimentary Sequencing</h3>";
        RudimentarySequencing += "These settings control what action the SCORM Player will take when a SCO exits.";
        RudimentarySequencing += "These settings not applicable to SCORM 2004 courses since SCORM 2004 Simple Sequencing";
        RudimentarySequencing += "allows the content to specify these behaviors. There are three factors the SCORM";
        RudimentarySequencing += "Player looks at when determining the action to take when a SCO exits: the position";
        RudimentarySequencing += "of the SCO in the course (is it in the middle, or is it the last SCO), the state";
        RudimentarySequencing += "of the SCO/Course, and the SCORM exit type specified by the SCO. NOTE: These settings";
        RudimentarySequencing += "only take affect when the content originates an exit action by calling LMSFinish";
        RudimentarySequencing += "before the learner initiates an exit action by using a navigational control in the";
        RudimentarySequencing += "SCORM Player.";
        RudimentarySequencing += "</div>";
        RudimentarySequencing += "<br /><hr /><br />";
        RudimentarySequencing += "<strong>Intermediate SCO</strong> - These settings apply to SCOs that are in the";
        RudimentarySequencing += "middle of the course (every SCO except for the last SCO).";
        RudimentarySequencing += "<br /><br />";
        RudimentarySequencing += "<table style=\"width: 100%\">";
        RudimentarySequencing += "<tr><td style=\"width: 50%;\">Course Satisifed</td><td>Course Not Satisifed</td></tr>";
        RudimentarySequencing += "<tr><td style=\"width: 50%\">Normal";
        RudimentarySequencing += "<select id=\"ddlIntNormalSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "<a href=\"#\">apply to all</a>";
        RudimentarySequencing += "</td><td>";
        RudimentarySequencing += "<select id=\"ddlIntNormalNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td></tr>";
        RudimentarySequencing += "<tr><td style=\"width: 50%\">";
        RudimentarySequencing += "Suspend<select id=\"ddlIntSuspendSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlIntSuspendNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td></tr>";
        RudimentarySequencing += "<tr><td style=\"width: 50%\">";
        RudimentarySequencing += "Timeout<select id=\"ddlIntTimeoutSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlIntTimeoutNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td></tr>";
        RudimentarySequencing += "<tr><td style=\"width: 50%\">";
        RudimentarySequencing += "Logout<select id=\"ddlIntLogoutSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlIntLogoutNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td></tr></table>";
        RudimentarySequencing += "<br /><br />";
        RudimentarySequencing += "<strong>Final SCO</strong> - These settings apply to the last SCO of the course.";
        RudimentarySequencing += "In the case of a single SCO course, the SCO is always treated as the final SCO.";     
        RudimentarySequencing += "<br /><br />";
        RudimentarySequencing += "<table style=\"width: 100%\">";
        RudimentarySequencing += "<tr>";
        RudimentarySequencing += "<td style=\"width: 50%\">";
        RudimentarySequencing += "Course Satisifed</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "Course Not Satisifed</td>";
        RudimentarySequencing += "</tr>";
        RudimentarySequencing += "<tr>";
        RudimentarySequencing += "<td style=\"width: 50%\">";
        RudimentarySequencing += "Normal";
        RudimentarySequencing += "<select id=\"ddlFinalNormalSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlFinalNormalNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "</tr>";
        RudimentarySequencing += "<tr>";
        RudimentarySequencing += "<td style=\"width: 50%\">";
        RudimentarySequencing += "Suspend<select id=\"ddlFinalSuspendSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlFinalSuspendNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "</tr>";
        RudimentarySequencing += "<tr>";
        RudimentarySequencing += "<td style=\"width: 50%\">";
        RudimentarySequencing += "Timeout";
        RudimentarySequencing += "<select id=\"ddlFinalTimeoutSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlFinalTimeoutNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "</tr>";
        RudimentarySequencing += "<tr>";
        RudimentarySequencing += "<td style=\"width: 50%\">";
        RudimentarySequencing += "Logout<select id=\"ddlFinalLogoutSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td>";
        RudimentarySequencing += "<td>";
        RudimentarySequencing += "<select id=\"ddlFinalLogoutNotSatisfied\">";
        RudimentarySequencing += "<option value=\"exit,no confirmation\">Exit course</option>";
        RudimentarySequencing += "<option value=\"exit,confirmation\">Exit course after confirm</option>";
        RudimentarySequencing += "<option value=\"continue\">Go to next SCO</option>";
        RudimentarySequencing += "<option value=\"message page\">Display message</option>";
        RudimentarySequencing += "<option value=\"do nothing\">Do nothing</option>";
        RudimentarySequencing += "</select>";
        RudimentarySequencing += "</td></tr></table>";
        RudimentarySequencing += "</div>";
        return RudimentarySequencing;
      }
      function BuildRudimentaryRollup()
      {
        var RudimentaryRollup = "<div id=\"tabs-4\">";
        RudimentaryRollup += "<div id=\"TabDescription4\">";
        RudimentaryRollup += "<h3>Rudimentary Rollup</h3>";
        RudimentaryRollup += "These settings specify how to score courses. These settings not applicable to SCORM ";
        RudimentaryRollup += "2004 courses since SCORM 2004 Simple Sequencing allows the content to specify these ";
        RudimentaryRollup += "behaviors.";
        RudimentaryRollup += "</div>";
        RudimentaryRollup += "<br /><hr /><br />";
        RudimentaryRollup += "Score Rollup Mode:&nbsp;";
        RudimentaryRollup += "<select id=\"ddlScoreRollupMode\">";
        RudimentaryRollup += "<option value=\"score provided by course\">Score Provided By Course</option>";
        RudimentaryRollup += "<option value=\"average score of all units\">Average Score of All Units</option>";
        RudimentaryRollup += "<option value=\"average score of all units with scores\">Average Score of All Units with Scores</option>";
        RudimentaryRollup += "<option value=\"fixed average\">Fixed Average</option>";
        RudimentaryRollup += "</select>";
        RudimentaryRollup += "<br /><br />";
        RudimentaryRollup += "*** Number of Scoring Objects:&nbsp;<input type=\"text\" />";
        RudimentaryRollup += "<br /><br /><br />";
        RudimentaryRollup += "Status Rollup Mode: &nbsp;";
        RudimentaryRollup += "<select id=\"ddlStatusRollupMode\">";
        RudimentaryRollup += "<option value=\"status provided by course\">Status Provided By Course</option>";
        RudimentaryRollup += "<option value=\"complete when all units complete\">Complete When All Units Complete</option>";
        RudimentaryRollup += "<option value=\"complete when all units satisfactorily complete\">Complete When All Units Satisfactorily Complete</option>";
        RudimentaryRollup += "<option value=\"complete when threshold score is met\">Complete When Threshold Score Is Met</option>";
        RudimentaryRollup += "<option value=\"complete when all units complete and threshold score is met\">Complete When All Units Complete And Threshold Score Is Met</option>";
        RudimentaryRollup += "</select>";
        RudimentaryRollup += "<br /><br />";
        RudimentaryRollup += "*** Threshold Score for Completion:&nbsp;<input type=\"text\" id=\"txtThresholdScoreForCompletion\" />&nbsp;&nbsp;(0.0 - 0.1)";
        RudimentaryRollup += "<br /><br /><br />";
        RudimentaryRollup += "<input type=\"checkbox\" id=\"ckbFirstScoIsPretest\" />&nbsp;First SCO is Pretest";
        RudimentaryRollup += "</div>";
        return RudimentaryRollup;
      }
      function BuildCompatibilitySettings()
      {
        var CompatibilitySettings = "<div id=\"tabs-5\">";
        CompatibilitySettings += "<div id=\"TabDescription5\">";
        CompatibilitySettings += "<h3>Compatibility Settings</h3>";
        CompatibilitySettings += "</div>";
        CompatibilitySettings += "<br /><hr /><br />";
        CompatibilitySettings += "<table width=\"100%\"><tr><td>";
        CompatibilitySettings += "<input type=\"checkbox\" id=\"ckbFinishCausesImmediateCommit\" />&nbsp;Finish Causes Immediate Commit";
        CompatibilitySettings += "<br />";
        CompatibilitySettings += "<input type=\"checkbox\" id=\"ckbLogoutCausesPlayerExit\" />&nbsp;Logout Causes Player Exit";
        CompatibilitySettings += "<br />";
        CompatibilitySettings += "<input type=\"checkbox\" id=\"ckbWrapSCOWindowWithAPI\" />&nbsp;Wrap SCO Window with API";
        CompatibilitySettings += "<br />";
        CompatibilitySettings += "<input type=\"checkbox\" id=\"ckbAlwaysFlowToFirstSCO\" />&nbsp;Always Flow to First SCO";
        CompatibilitySettings += "<br />";
        CompatibilitySettings += "<input type=\"checkbox\" id=\"ckbRawScoreCanActAsScaledScore\" />&nbsp;Raw Score Can Act as Scaled Score";
        CompatibilitySettings += "<br />";
        CompatibilitySettings += "<input type=\"checkbox\" id=\"ckbRollupEmptySetToUnknownLookAhead\" />&nbsp;Rollup Empty Set to Unknown Look-Ahead";
        CompatibilitySettings += "<br /><br />";
        CompatibilitySettings += "Sequencer Mode:";
        CompatibilitySettings += "<select id=\"ddlSequencerMode\">";
        CompatibilitySettings += "<option value=\"disabled\">Disabled</option>";
        CompatibilitySettings += "<option value=\"enabled\">Enabled</option>";
        CompatibilitySettings += "</select>";
        CompatibilitySettings += "<br /><br />";
        CompatibilitySettings += "Reset RunTime Data Timing:";
        CompatibilitySettings += "<select id=\"ddlResetRunTimeDataTiming\">";
        CompatibilitySettings += "<option value=\"never\">Never</option>";
        CompatibilitySettings += "<option value=\"when exit is not suspend\">When Exit Is Not Suspend</option>";
        CompatibilitySettings += "<option value=\"on each new sequencing attempt\">On Each New Sequencing Attempt</option>";
        CompatibilitySettings += "</select>";
        CompatibilitySettings += "<br /><br />";
        CompatibilitySettings += "</td></tr></table>";
        CompatibilitySettings += "</div>";
        return CompatibilitySettings;
      }
      function BuildCommunicationSettings()
      {
        var CommunicationSettings = "<div id=\"tabs-6\">";
        CommunicationSettings += "<div id=\"Div1\">";
        CommunicationSettings += "<h3>Communication Settings</h3>";
        CommunicationSettings += "These settings affect how the player saves course status.";
        CommunicationSettings += "</div>";
        CommunicationSettings += "<br /><hr /><br />";
        CommunicationSettings += "<table width=\"100%\"><tr><td>";
        CommunicationSettings += "Maximum Failed Attempts:&nbsp;<input type=\"text\" id=\"txtMaximumFailedAttempts\" />";
        CommunicationSettings += "<br /><br />";
        CommunicationSettings += "Commit Frequency:&nbsp;<input type=\"text\" id=\"txtCommitFrequency\" />&nbsp;milliseconds";
        CommunicationSettings += "</td></tr></table>";
        CommunicationSettings += "</div>";
        return CommunicationSettings;
      }
      function BuildDebuggerOptions()
      {
        var DebuggerOptions = "<div id=\"tabs-7\">";               
        DebuggerOptions += "<div id=\"Div2\">";
        DebuggerOptions += "<h3>Debugger Options</h3>";
        DebuggerOptions += "This section contains settings related to client-side (browser) logging.";
        DebuggerOptions += "</div>";
        DebuggerOptions += "<br /><hr /><br />";
        DebuggerOptions += "<table width=\"100%\"><tr><td>";
        DebuggerOptions += "Control: &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-control\" id=\"rdoControlOff\" />&nbsp;off &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-control\" id=\"rdoControlAudit\" />&nbsp;audit&nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-control\" id=\"rdoControlDetailed\" />&nbsp;detailed";
        DebuggerOptions += "<br /><br />";
        DebuggerOptions += "Runtime: &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-runtime\" id=\"rdoRuntimeOff\" />&nbsp;off &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-runtime\" id=\"rdoRuntimeAudit\" />&nbsp;audit&nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-runtime\" id=\"rdoRuntimeDetailed\" />&nbsp;detailed";
        DebuggerOptions += "<br /><br />";
        DebuggerOptions += "Sequencing: &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-seq\" id=\"rdoSequencingOff\" />&nbsp;off &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-seq\" id=\"rdoSequencingAudit\" />&nbsp;audit &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-seq\" id=\"rdoSequencingDetailed\" />&nbsp;detailed";
        DebuggerOptions += "<br /><br />";
        DebuggerOptions += "Look-ahead: &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-lookahead\" id=\"rdoLookOff\" />&nbsp;off &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-lookahead\" id=\"rdoLookAudit\" />&nbsp;audit &nbsp;&nbsp;&nbsp;";
        DebuggerOptions += "<input type=\"radio\" name=\"debugging-lookahead\" id=\"rdoLookDetailed\" />&nbsp;detailed";
        DebuggerOptions += "<br /><br />";
        DebuggerOptions += "<input type=\"checkbox\" id=\"ckbIncludeTimestamps\" />&nbsp;Include Timestamps";
        DebuggerOptions += "</td></tr></table>";
        DebuggerOptions += "</div>";
        return DebuggerOptions;
      }
      function BuildPresets()
      {
        var Presets = "<div id=\"tabs-8\">";
        Presets += "<div id=\"Div3\">";
        Presets += "<h3>Presets</h3>";
        Presets += "Create a new preset based on your currently configured package properties or apply previously created settings.";
        Presets += "</div>";
        Presets += "<br /><hr /><br />";
        Presets += "<table width=\"100%\"><tr><td></td></tr></table>";
        Presets += "</div>";
        return Presets;
      }
      
      function BuildSaveBar()
      {
        var SaveBar = "";
        SaveBar += "<div id=\"SaveBar\" style=\"background-color: AliceBlue; border: solid 1px grey; height: 25px;padding: 10px 10px 10px 10px;\">";
        SaveBar += "<div id=\"Message\" style=\"float: left\"></div>";
        SaveBar += "<div id=\"SaveButton\" style=\"float: right\">";
        SaveBar += "<input type=\"button\" id=\"btnSave\" value=\"Save Changes\" />";
        SaveBar += "</div>";
        SaveBar += "</div>";
        return SaveBar;
      }
      
      
      
      
      
      
      
      
      
      
      
      
      /*
      Utilities
      */
      function ConvertYesNo(value)
      {
        if(value=='1' || value.toLowerCase()=="yes" || value == true)
        {
        return true;
        }else{
        return false;
        }
      }
      
      //kick it all off...
      dostuff();