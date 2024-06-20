  function toggleNavigation() {
    var navigation = document.getElementById('navigation');
    var hamburger = document.querySelector('.hamburger');
    var navigationLinks = navigation.querySelectorAll('a'); 
    
    navigation.classList.toggle('active');
    hamburger.classList.toggle('active');
    
    navigationLinks.forEach(function(link) {
      link.classList.toggle('hidden-text');
    });
  }

  function toggleZoom() {
    var currentZoom = parseFloat(document.body.style.zoom) || 1; 
    var newZoom = currentZoom === 1 ? 1.5 : 1; 
    document.body.style.zoom = newZoom;
  }

  function zoomProfileIcon() {
    var profileIcon = document.querySelector('.profile-icon i');
    profileIcon.style.transform = profileIcon.style.transform === 'scale(1.2)' ? 'scale(1)' : 'scale(1.2)';
  }

  function showDashboard() {
    var defaultDashboard = document.getElementById('defaultDashboard');
    var barangayOfficialsDashboard = document.getElementById('barangayOfficialsDashboard');
    var blotterDashboard = document.getElementById('blotterDashboard');
    var formContainer = document.getElementById('formContainer');
    defaultDashboard.style.display = 'flex';
    barangayOfficialsDashboard.style.display = 'none';
    blotterDashboard.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showOfficials() {
    var defaultDashboard = document.getElementById('defaultDashboard');
    var barangayOfficialsDashboard = document.getElementById('barangayOfficialsDashboard1');
    var blotterDashboard = document.getElementById('blotterDashboard');
    var formContainer = document.getElementById('formContainer');
    defaultDashboard.style.display = 'none';
    barangayOfficialsDashboard.style.display = 'flex';
    blotterDashboard.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showBlotter() {
    var defaultDashboard = document.getElementById('defaultDashboard');
    var barangayOfficialsDashboard = document.getElementById('barangayOfficialsDashboard');
    var blotterDashboard = document.getElementById('blotterDashboard');
    var formContainer = document.getElementById('formContainer');
    defaultDashboard.style.display = 'none';
    barangayOfficialsDashboard.style.display = 'none';
    blotterDashboard.style.display = 'flex';
    formContainer.style.display = 'none';
  }

  function showForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('formContainer');
    var blurredBackground = document.createElement('div'); 
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideForm() {
      var overlay = document.getElementById('overlay');
      var formContainer = document.getElementById('formContainer');
      var blurredBackground = document.querySelector('.blurred-background'); 
      blurredBackground.parentNode.removeChild(blurredBackground);

      overlay.style.display = 'none';
      formContainer.style.display = 'none';
  }

  function showActivityForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('activityContainer');
    var blurredBackground = document.createElement('div'); 
    blurredBackground.classList.add('blurred-background'); 
    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideActivityForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('activityContainer');
    var blurredBackground = document.querySelector('.blurred-background'); 

    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showUserForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('userContainer');
    var blurredBackground = document.createElement('div'); 
    blurredBackground.classList.add('blurred-background'); 

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideUserForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('userContainer');
    var blurredBackground = document.querySelector('.blurred-background'); 

    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showBlotterForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('blotterFormContainer');
    var blurredBackground = document.createElement('div'); 
    blurredBackground.classList.add('blurred-background'); 

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideBlotterForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('blotterFormContainer');
    var blurredBackground = document.querySelector('.blurred-background'); 

    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showUsers() {
    var usersDashboard = document.getElementById('usersDashboard');
    usersDashboard.style.display = 'block';

    var defaultDashboard = document.getElementById('defaultDashboard');
    var barangayOfficialsDashboard = document.getElementById('barangayOfficialsDashboard');
    var blotterDashboard = document.getElementById('blotterDashboard');

    defaultDashboard.style.display = 'none';
    barangayOfficialsDashboard.style.display = 'none';
    blotterDashboard.style.display = 'none';

    var formContainer = document.getElementById('userFormContainer');
    formContainer.style.display = 'none';
  }

  function showResidentForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('residentFormContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideResidentForm() {
      var overlay = document.getElementById('overlay');
      var formContainer = document.getElementById('residentFormContainer');
      var blurredBackground = document.querySelector('.blurred-background'); 
      blurredBackground.parentNode.removeChild(blurredBackground);

      overlay.style.display = 'none';
      formContainer.style.display = 'none';
  }
  function searchResidents() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("residentsSearchInput");
      filter = input.value.toUpperCase();
      table = document.getElementsByTagName("table")[2]; 
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  function toggleProfileDetails() {
    var profileDetailsContainer = document.getElementById("profileDetailsContainer");
    if (profileDetailsContainer.style.display === "none") {
        profileDetailsContainer.style.display = "block";
    } else {
        profileDetailsContainer.style.display = "none";
    }
  }

  let dropdownContent = document.getElementById('dropdownContent');
    
  let currentURI = window.location.href;
  let links = dropdownContent.getElementsByTagName('a');
  for (let i = 0; i < links.length; i++) {
        if (links[i].href === currentURI) {
          dropdownContent.style.display = 'block';
          links[i].classList.add('active'); 
          break;
        }
  }

  function toggleDropdown() {
    if (dropdownContent.style.display === 'block') {
      dropdownContent.style.display = 'none';
    } else {
      dropdownContent.style.display = 'block';
    }
  }

  var currentPage = window.location.href;

  if (currentPage.includes("AdminDashboard.php")) {
      var dashbLink = document.getElementById('dashb');
      dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("BarangayOfficial.php")) {
      var dashbLink = document.getElementById('official');
      dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Activity.php")) {
      var dashbLink = document.getElementById('act');
      dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Blotter.php")) {
      var dashbLink = document.getElementById('blo');
      dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("BarangayContact&Message.php")) {
    var dashbLink = document.getElementById('cont');
    dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Document.php")) {
    var dashbLink = document.getElementById('docu');
    dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("BarangayFAQ.php")) {
    var dashbLink = document.getElementById('faq1');
    dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Forms.php")) {
    var dashbLink = document.getElementById('forms');
    dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Information.php")) {
    var dashbLink = document.getElementById('info');
    dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Resident.php")) {
    var dashbLink = document.getElementById('resident');
    dashbLink.classList.add('active');
  }

  var currentPage = window.location.href;

  if (currentPage.includes("Users.php")) {
    var dashbLink = document.getElementById('user');
    dashbLink.classList.add('active');
  }

  function showIntroForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('infoContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    var existingText = document.querySelector('.dashboard-box1#db1 .textcon p').textContent;
    var textarea = formContainer.querySelector('textarea');
    textarea.value = existingText.trim();

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideIntroForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('infoContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showMisForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('MisContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideMisForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('MisContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showVisForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('visContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideVisForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('visContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showGoalForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('goalContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideGoalForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('goalContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showStaForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('staContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideStaForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('staContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showPopForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('popContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hidePopForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('popContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showPSForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('psContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hidePSForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('psContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showPEAForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('peaContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hidePEAForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('peaContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showBEForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('befContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideBEForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('befContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  function showIncomeForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('incomeContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideIncomeForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('incomeContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }
  
  function showHisForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('hisContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideHisForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('hisContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }

  
  function showMapForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('mapContainer');
    var blurredBackground = document.createElement('div');
    blurredBackground.classList.add('blurred-background');

    document.body.appendChild(blurredBackground);

    overlay.style.display = 'block';
    formContainer.style.display = 'block';
  }

  function hideMapForm() {
    var overlay = document.getElementById('overlay');
    var formContainer = document.getElementById('mapContainer');
    var blurredBackground = document.querySelector('.blurred-background');
    
    blurredBackground.parentNode.removeChild(blurredBackground);

    overlay.style.display = 'none';
    formContainer.style.display = 'none';
  }