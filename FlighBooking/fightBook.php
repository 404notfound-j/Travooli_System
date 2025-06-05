<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Travooli Flight Search</title>
  <link rel="stylesheet" href="./css/booking.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header>
  <?php include 'header.php'; ?>
  </header>
  <div class="filter-bar">
    <div class="filter-bar-inner">
      <div class="filter-box"><span class="icon"><i class="fa-solid fa-plane-departure"></i></span><span id="from">From?</span></div>
      <div class="filter-box"><span class="icon"><i class="fa-solid fa-plane-arrival"></i></span><span id="to">To?</span></div>
      <div class="filter-box"><span class="icon"><i class="fa-solid fa-calendar-days"></i></span><span id="date">Depart - Return</span></div>
      <div class="filter-box"><span class="icon"><i class="fa-solid fa-user"></i></span><span id="passengers">1 adult</span></div>
      <button class="search-btn" onclick="window.location.href='results.html'">Search</button>
    </div>
  </div>
  <div class="main-content">
    <div class="filters">
      <h2>Filters</h2>
      <div class="filter-group">
        <h4>Seat class</h4>
        <button class="filter-button">Economy</button>
        <button class="filter-button">Premium Economy</button>
        <button class="filter-button">Business Class</button>
        <button class="filter-button">First Class</button>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Time</h4>
          <input type="range" id="timeRange" min="360" max="1380" step="30" value="360">
          <div class="range-labels">
          <span id="startTime">6:00AM</span>
          <span>11:00PM</span>
        </div>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Rating</h4>
        <div class="rating-buttons">
          <button>0+</button>
          <button>1+</button>
          <button>2+</button>
          <button>3+</button>
          <button>4+</button>
        </div>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Airlines</h4>
        <div class="checkbox-group">
          <label><input type="checkbox">Emirated</label>
          <label><input type="checkbox">Fly Dubai</label>
          <label><input type="checkbox">Qatar</label>
          <label><input type="checkbox">Etihad</label>
        </div>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Trips</h4>
        <div class="checkbox-group">
          <label><input type="checkbox">Round trip</label>
          <label><input type="checkbox">On Way</label>
        </div>
      </div>
      <div class="action-buttons">
        <button class="cancel-btn" onclick="location.reload();">Cancel</button>
        <button class="apply-btn">Apply Filters</button>
      </div>
    </div>
    <div class="main-panel">
      <div class="sort-bar">
        <div class="sort-option">Cheapest<br><span>$99 . 2h 18m</span></div>
        <div class="sort-option selected">Best<br><span>$99 . 2h 18m</span></div>
        <div class="sort-option" id="sortQuickest">Quickest<br><span>$99 · 2h 18m</span></div>
        <div class="sort-other"><span>&#9776;</span><span>Other sort</span></div>
      </div>
  <div id="flightResults" class="results-container"></div>
</div>

<script src="js/flightBook.js"> </script>

</body>
</html>

