(function () {
    const second = 1000,
          minute = second * 60,
          hour = minute * 60,
          day = hour * 24;

        let offer= global_date; //to determine target time

        var times = offer.split(" ");
        var dt = times[0];
        var tm = times[1];

        // format ex : 2022-05-01
        var dtp = dt.split("-");
        var yr = parseInt(dtp[0]); //year
        var mth = parseInt(dtp[1]); //month
        var dy = parseInt(dtp[2]); //day

        //format time ex : 16:00:00
        var tms = tm.split(":");
        var hr = parseInt(tms[0]);
        var mn = parseInt(tms[1]);

        //string date and time :: sec always 0 so that easy to count
        var fmt = mth+"/"+dy+"/"+yr+" "+hr+":"+mn+":"+0;  
        countDown = new Date(fmt).getTime(),

        x = setInterval(function() {    
          let now = new Date().getTime(),
              distance = countDown - now;

          //days
          var days = Math.floor(distance / (day));
          if(days < 10)
          {
            days = "0"+days;
          }

          //hours
          var hours = Math.floor((distance % (day)) / (hour));
          if(hours < 10)
          {
            hours = "0"+hours;
          }

          //minute
          var mint = Math.floor((distance % (hour)) / (minute));
          if(mint < 10)
          {
            mint = "0"+mint;
          }

          //sec
          var sec = Math.floor((distance % (minute)) / second);
          if(sec < 10)
          {
            sec = "0"+sec;
          }
   
          //do something later when date is reached
          if (distance <= 0) {
              
            // let headline = document.getElementById("headline"),
            //     countdown = document.getElementById("countdown"),
            //     content = document.getElementById("content");
   
            // headline.innerText = "Ohhh no! Offer has ended";
            // countdown.style.display = "none";
            // content.style.display = "block";
            days = hours = mint = sec = "00";
            clearInterval(x);
          }

          document.getElementById("days").innerText = days,
          document.getElementById("hours").innerText = hours,
          document.getElementById("minutes").innerText = mint,
          document.getElementById("seconds").innerText = sec
          ;
          //seconds
        }, 0)
    }());