$(window).scroll(function() {
    var scrollPosition = $(this).scrollTop();
    var shadowIntensity = Math.min(scrollPosition / 50, 20); // Adjust divisor to control shadow intensity

    $('.header').css('box-shadow', '0 0 ' + shadowIntensity + 'px rgba(0, 0, 0, 0.5)');
});

function displayText(text) {
    document.getElementById("display").innerText = text;
  }

  function clickMissionButton() {
    var missionBtn = document.getElementById("missionBtn");
    missionBtn.click(); // Simulating click event
  }

  window.onload = function() {
    var missionText = document.getElementById("display").innerText;
    displayText(missionText); // Display the mission text initially
  };



  function forceDownload() {
    var url = "pdf_file/CRISTAL PORTFOLIO.pdf";
    var fileName = url.substring(url.lastIndexOf('/') + 1);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.responseType = "blob";
    xhr.onload = function() {
      var blob = xhr.response;
      var link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      link.download = fileName;
      link.click();
    };
    xhr.send();
  }

  
  
  
  