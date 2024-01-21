document.addEventListener("DOMContentLoaded", function(){
    openRTSE_Widget();
});

function openRTSE_Widget()
{
    let Seconds = 30000;
    if(rtse_frontend_ajax_object.seconds_to_open)
    {
        if(rtse_frontend_ajax_object.seconds_to_open != '')
        {
            let seconds_to_open = rtse_frontend_ajax_object.seconds_to_open;
            Seconds = (seconds_to_open*1000);
        }
    }

    setTimeout( function(){
        if(document.getElementById("rtse-rating-widget"))
        {
            var name = 'rtse-hide-rating-widget'; 
		    if(RTSEGetCookies(name) != 1)
            {
                var RTSE_Widget = document.getElementById("rtse-rating-widget");
                RTSE_Widget.classList.add("open");
                document.querySelector('html').classList.add('overflow-hidden');
            }
        }
    },Seconds);
}

document.addEventListener("DOMContentLoaded", function(){

    if(document.getElementById("li-rtse-satisfied"))
    {
        var rtse_satisfied_list= document.getElementById("li-rtse-satisfied");
        let rtse_satisfied_ul_li = rtse_satisfied_list.querySelectorAll('ul li');
        rtse_satisfied_ul_li.forEach((satisfied_li) => {
            satisfied_li.addEventListener('click', function (ele) {
                rtse_satisfied_list.querySelector('ul').classList.add('active');
                let all_ul_li = document.querySelectorAll('.li-rtse-satisfied ul li');
                all_ul_li.forEach((all_li) => {
                    all_li.classList.remove('active');
                });
                
                let p = satisfied_li.classList.add("active");
                var RTSE_Widget_submit = document.getElementById("rtse-submit-btn");
                RTSE_Widget_submit.classList.add('enable');
                // satisfied_li.parentNode.classList.add('active');
            });
        });
    }

    if(document.getElementById("rtse-rating-widget"))
    {
        var RTSE_Widget = document.getElementById("rtse-rating-widget");
        var RTSE_WidgetClose = RTSE_Widget.getElementsByClassName("rtse-rating-widget-close-btn")[0];
        RTSE_WidgetClose.onclick = function() 
        {
            document.querySelector('html').classList.remove('overflow-hidden');
            var days = 30;
            if(rtse_frontend_ajax_object.number_of_days_decline)
            {
                var days_to_close = rtse_frontend_ajax_object.number_of_days_decline;
                if(days_to_close != '')
                {
                    days = days_to_close;
                }
            }
            //RTSE_Widget.style.display = "none";
            RTSE_Widget.classList.remove("open");
            document.querySelector('html').classList.remove('overflow-hidden');
            var name = 'rtse-hide-rating-widget'; 
            var value = '1';
            RTSEsetCookie(name, value, days);
        }
    }

    if(document.getElementById("rtse-submit-btn"))
    {
        document.getElementById("RTSEPleaseWaitMsgPopup").style.display = 'none';
        var RTSE_Submit_btn = document.getElementById("rtse-submit-btn");
        RTSE_Submit_btn.addEventListener('click', function (ele) {
            if(RTSE_Submit_btn.classList.contains('enable'))
            {
                if(document.querySelector('.li-rtse-satisfied ul li.active'))
                {
                    let rating_li = document.querySelector('.li-rtse-satisfied ul li.active');
                    if(rating_li)
                    {
                        document.getElementById("RTSEPleaseWaitMsgPopup").style.display = 'block';
                        let ratings = rating_li.innerHTML;
                        let rtse_save_rating_nonce = document.getElementById("rtse_save_rating_nonce").value
                        if(ratings)
                        {
                            var str = '&ratings='+ratings+'&action=rtse_save_ratings&rtse_save_rating_nonce='+rtse_save_rating_nonce;
                            var request = new XMLHttpRequest();
                            request.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    var response = JSON.parse(request.response);
                                    if(response.status)
                                    {
                                        if(response.status == "success")
                                        {
                                            var days = 30;
                                            if(document.getElementById("rtse-success-widget"))
                                            {
                                                setTimeout( function(){
                                                    var RTSE_Widget = document.getElementById("rtse-rating-widget");
                                                    RTSE_Widget.classList.remove("open");
                                                },500);
                                            
                                                setTimeout( function(){
                                                    var RTSE_Success_widget = document.getElementById("rtse-success-widget");
                                                    RTSE_Success_widget.classList.add("open");
                                                    document.getElementById("RTSEPleaseWaitMsgPopup").style.display = 'none';
                                                },1000);
                                                document.querySelector('html').classList.add('overflow-hidden');
                                                if(rtse_frontend_ajax_object.number_of_days_submit)
                                                {
                                                    var days_to_close = rtse_frontend_ajax_object.number_of_days_submit;
                                                    if(days_to_close != '')
                                                    {
                                                        days = days_to_close;
                                                    }
                                                }
                                            }
                                            var name = 'rtse-hide-rating-widget'; 
                                            var value = '1';
                                            RTSEsetCookie(name, value, days);
                                        }
                                        else if(response.status == "error")
                                        {
                                            console.log("Error");
                                        }
                                    }
                                }
                            };
                            request.overrideMimeType("application/json");
                            request.open("POST", rtse_frontend_ajax_object.ajaxurl, true);
                            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                            request.send(str);
                        }
                    }
                }
                else
                {
                    console.log("Please select ratings!")
                }
            }
            else
            {
                console.log("Please select ratings!")
            }
        });
    }
    
    if(document.getElementById("rtse-widget-success-close-btn"))
    {
        var RTSESussessWidget = document.getElementById("rtse-success-widget");
        var RTSESussessWidgetclose = document.getElementById("rtse-widget-success-close-btn")
        RTSESussessWidgetclose.onclick = function() 
        {
            RTSESussessWidget.classList.remove("open");
            document.querySelector('html').classList.remove('overflow-hidden');
        }
    }
});


function RTSEsetCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function RTSEGetCookies(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function RTSEeraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}