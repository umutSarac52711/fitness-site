// Ensure calculateBMI is globally accessible - this was the previous approach.
// window.calculateBMI = function() { ... }

// Keep calculateBMI function defined as it was, but it won't be directly global if app.js is an IIFE or module.
function calculateBMI() {
    const heightInput = document.getElementById('bmiHeight');
    const weightInput = document.getElementById('bmiWeight');
    const resultDiv = document.getElementById('bmiResult');

    // Clear previous results or error messages
    if(resultDiv) resultDiv.innerHTML = '';

    if (!heightInput || !weightInput || !resultDiv) {
        console.error('BMI calculator elements not found. Ensure bmiHeight, bmiWeight, and bmiResult IDs exist.');
        if(resultDiv) resultDiv.innerHTML = '<p class="text-danger">Error: Calculator elements missing.</p>';
        return;
    }

    const height = parseFloat(heightInput.value);
    const weight = parseFloat(weightInput.value);

    if (isNaN(height) || height <= 0 || isNaN(weight) || weight <= 0) {
        resultDiv.innerHTML = '<p class="text-danger">Please enter valid positive numbers for height (cm) and weight (kg).</p>';
        return;
    }

    const heightInMeters = height / 100;
    const bmi = weight / (heightInMeters * heightInMeters);
    const bmiRounded = bmi.toFixed(1);

    let category = '';
    if (bmi < 18.5) {
        category = 'Underweight';
    } else if (bmi >= 18.5 && bmi <= 24.9) {
        category = 'Healthy';
    } else if (bmi >= 25 && bmi <= 29.9) {
        category = 'Overweight';
    } else {
        category = 'Obese';
    }

    resultDiv.innerHTML = `
      <h4 class=\"mt-3\" style=\"color:white\">Your BMI: ${bmiRounded}</h4>
      <p>Category: <strong>${category}</strong></p>
    `;
}

(function () {
  document.addEventListener('DOMContentLoaded', function() {
    const latestPostsWrap = document.getElementById('latest-posts-container');
    const commentsWrap = document.getElementById('comments-section-ajax'); // Target for comments

    async function reloadLatest() {
      if (!latestPostsWrap) return;
      try {
        const res = await fetch(`${BASE_URL}/ajax/latest-posts.php`);
        if (!res.ok) throw new Error(`Latest posts fetch failed: ${res.status}`);
        latestPostsWrap.innerHTML = await res.text();
      } catch (err) {
        console.warn('Latest posts reload failed:', err);
        latestPostsWrap.innerHTML = '<p class="small text-muted">Failed to load latest posts.</p>';
      }
    }

    async function reloadComments() {
      if (!commentsWrap) return;
      const postSlug = commentsWrap.dataset.postSlug; // Get slug from data attribute
      if (!postSlug) {
        console.warn('Post slug not found for reloading comments.');
        commentsWrap.innerHTML = '<p class="small text-muted">Cannot load comments: Post identifier missing.</p>';
        return;
      }

      try {
        const res = await fetch(`${BASE_URL}/ajax/get-comments.php?slug=${encodeURIComponent(postSlug)}`);
        if (!res.ok) throw new Error(`Comments fetch failed: ${res.status}`);
        commentsWrap.innerHTML = await res.text();
      } catch (err) {
        console.warn('Comments reload failed:', err);
        commentsWrap.innerHTML = '<p class="small text-muted">Failed to load comments.</p>';
      }
    }

    // Initial loads
    if (latestPostsWrap) {
      reloadLatest();
      setInterval(reloadLatest, 30000); // Keep reloading latest posts
    }
    if (commentsWrap) {
      reloadComments();
      setInterval(reloadComments, 20000); // Reload comments every 20 seconds
    }

    // Re-establish event listener for the BMI button
    const calculateBmiButton = document.getElementById('calculateBmiBtn');
    if (calculateBmiButton) {
      calculateBmiButton.addEventListener('click', calculateBMI); // Call calculateBMI on click
    }

  }); // End DOMContentLoaded listener
})();

function calculateBmiAccount() {
    const weightInput = document.getElementById('log_weight');
    const heightInput = document.getElementById('log_height');
    const bmiDisplay = document.getElementById('log_bmi_display');

    const weight = parseFloat(weightInput.value);
    const height = parseFloat(heightInput.value);

    if (weight > 0 && height > 0) {
        const heightInMeters = height / 100;
        const bmi = weight / (heightInMeters * heightInMeters);
        bmiDisplay.value = bmi.toFixed(2);
    } else {
        bmiDisplay.value = '';
    }
}

// Optional: Trigger calculation if fields already have values on page load (e.g. from server-side validation repopulation)
// document.addEventListener('DOMContentLoaded', calculateBmiAccount);
