M.OUE = M.OUE || {};
M.OUE.blockname = 'bath_oue';
M.OUE.remoteURI = M.cfg.wwwroot + '/blocks/' + M.OUE.blockname + '/stats.php';
M.OUE.init = function (Y,username) {
    YUI().use('overlay', 'anim', 'transition', 'io', 'graphics', 'json-parse', 'cookie', function (Y) {
        var overlay = new Y.Overlay({
            srcNode: '#oue_notice',
            visible: true,
            xy: [60, 0]
        });

        M.OUE.onStart = function () {
            //Event handler called when the transaction begins
            //We show the loader image
            var loaderGIF = M.util.image_url('image_440638', 'block_' + M.OUE.blockname);
            Y.one('#survey_loading').setStyle('height', '64px')
            Y.one('#survey_loading').setStyle('background', 'url(' + loaderGIF + ') center no-repeat')
        }
        var onEnd = function (days) {
            //Dismiss the message for the number of days given
            //Sets a cookie
            var today = new Date();
            var expiryDate = new Date();
            expiryDate.setDate(today.getDate() + days);
            if (!Y.Cookie.exists(M.OUE.blockname)) {
                Y.Cookie.setSub(M.OUE.blockname, 'date', expiryDate, {
                    expires: new Date(expiryDate)
                });
            }

        }
        //Close the notice
        /*Y.one('#notice_dismiss').on('click', function (e) {
            e.preventDefault();
            var anim = new Y.Anim({
                node: '#oue_notice',
                duration: 0.6,
                to: {
                    opacity: 0
                }
            });
            anim.run();
            anim.on('end', onEnd(3));
        });*/
        M.OUE.progressBar = function (Y, total, completed) {
            if (completed >= 0) {
                var html5ProgressBar = Y.one('#progressbar');
                if (Y.UA.ie > 0) {
                	html5ProgressBar.setStyle('border-right-style','none');
                	html5ProgressBar.setStyle('border-left-style','none'); //Anoying progress bar borders still show up in IE 
                } 
                html5ProgressBar.set('max', total);
                html5ProgressBar.set('value', completed);
                html5ProgressBar.show();
                /* HANDLE SVG FOR FALLBACK */
                var svgGraphic = new Y.Graphic({
                    render: "#svg-container"
                });
                
                var completedInPixels = completed * 200 / total;
                /*var completedText = 'Completed';
                var completedInPerc = completed * 100 / total;
                completedInPerc = Math.round(completedInPerc);
                if(completedInPerc > 50)
                {
                	completedText = 'Remaining ';
                	completedInPerc = 100 - completedInPerc;
                }*/
               // var progress_label = Y.one('#progress_label');
                //progress_label.set('innerHTML', completedText + ': <span class="oue_number">' + completedInPerc + '%</span>');
                var totalRectangle = svgGraphic.addShape({
                    type: "rect",
                    stroke: {
                        color: '#491155',
                        weight: 1
                    },
                    fill: {
                        color: "#fff",
                        opacity: 1,
                    },
                    width: 200,
                    height: 16
                });
                var completedRectangle = svgGraphic.addShape({
                    type: 'rect',
                    fill: {
                        color: '#712C7F'
                    },
                    width: completedInPixels,
                    height: 15,
                    stroke: {
                        weight: 0
                    }
                });

            }
        }
        M.OUE.handleSuccess = function (id, response, data) {

            Y.one('#survey_loading').setStyle('display', 'none'); //Hide the loader
            Y.one('#survey_container').setStyle('display', 'block'); // Display survey container
            var blShowNotice = true;
            var surveyData = Y.JSON.parse(response.responseText);
            var today = new Date();
            var totalSurveys = 0;
            var completedSurveys = 0;
            var incompletedSurveys = 0;
            //console.log(surveyData);
 
             if (surveyData.hasOwnProperty('sits_error')) {
                M.OUE.handleFailure(id, response, '001');
                blShowNotice = false;
            } 
            else if (surveyData.hasOwnProperty('result_error')) {
                M.OUE.handleFailure(id, response, '002');
                blShowNotice = false;
            } else {
                if (surveyData.data == 'NO ROWS') {
					//Hide the complete now button
					Y.one('#oue_link').hide();
                    Y.one('#no_survey_results').show();
                    blShowNotice = false;
                } else {
                	//We have data

                		//We have real data
                		completedSurveys =  Number(surveyData.data.C);
                		incompletedSurveys = Number(surveyData.data.S);
 
                    /*for (i = 0; i < surveyData.length; i++) {
                        if (surveyData[i].OBA_STAS == 'C') {
                            completedSurveys = Number(surveyData[i].TOTAL);
                        }
                        if (surveyData[i].OBA_STAS == 'S') {
                            incompletedSurveys = Number(surveyData[i].TOTAL);
                        }
                    }*/
					totalSurveys  = completedSurveys + incompletedSurveys; //Total Surveys
                    //Show the progress bar now
                    //totalSurveys = completedSurveys = 4;
                    if(totalSurveys == completedSurveys) //All done!
					{
						//hide the notification
						blShowNotice = false;
						//Hide the complete now button
						Y.one('#oue_link').hide();
						//replace the progress bar with text
						Y.one('#no_survey_results').show();
					}
					else{
						//var survey_progress = M.str.block_bath_oue.survey_message + ' <span class="oue_number">' + completedSurveys + '</span> out of  <span class="oue_number"> ' + totalSurveys + '</span> surveys';
	                    var survey_progress = 'You have completed <span class="oue_number">'+ completedSurveys + '</span>  out of <span class="oue_number">'+ totalSurveys + '</span> of your current active unit evaluations';
	                    Y.one('.survey_progress').set('innerHTML',survey_progress);
	                    var notificationText = 'You have completed <span class="oue_number">'+ completedSurveys + '</span>  out of <span class="oue_number">'+ totalSurveys + '</span> of your current active unit evaluations';
	                    Y.one('#oue_notice').set('innerHTML',notificationText);
	                    Y.one('#notice_links').appendTo(Y.one('#oue_notice')).show();
						M.OUE.progressBar(Y, totalSurveys, completedSurveys);
					}
                }
            } //End else
			
			 Y.one('#oue_notice').hide();
            //Only Show this if the cookie has expired
            if (!Y.Cookie.exists(M.OUE.blockname) && blShowNotice) {
                    Y.one('#oue_notice').show();
                    overlay.render('#page-wrapper');
            } 
        } //End of handleSuccess
        M.OUE.handleFailure = function (id, response, data) {
            Y.log("ERROR " + id + " " + response.responseText, "info", "OUE"); // Put a log msg
            Y.one('#survey_loading').setStyle('display', 'none'); //hide the loader
            Y.one('#global_error_msg').show().append('<br/>Error: '+data); // show the error message
			
        }//End of handleFailure
        
        var oue_container = Y.one('#oue_notice');
        //This will get the data using IO
        var cfg = {
            data: {
                'username': username.username
            },
            dataType: 'json',
            on: {
                start: M.OUE.onStart,
                success: M.OUE.handleSuccess,
                failure: M.OUE.handleFailure
            }
        };
        var request = Y.io(M.OUE.remoteURI, cfg); // Send Ajax Request
    });
}
