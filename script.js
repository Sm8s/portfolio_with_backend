
function filterTasks() {
  const query = document.getElementById('search').value.toLowerCase();
  const tasks = document.querySelectorAll('article.task');
  tasks.forEach(task => {
    const title = task.getAttribute('data-title');
    if (title.includes(query)) {
      task.style.display = '';
    } else {
      task.style.display = 'none';
    }
  });

  // Update progress after filtering
  updateProgress();
}

// Fortschritt berechnen und anzeigen
function updateProgress() {
  const allTasks = Array.from(document.querySelectorAll('article.task'));
  // Only count tasks that are visible (after filtering)
  const visibleTasks = allTasks.filter(task => task.style.display !== 'none');
  const totalTasks = visibleTasks.length;
  const completedTasks = visibleTasks.filter(task => task.classList.contains('completed')).length;
  const percentage = totalTasks === 0 ? 0 : Math.round((completedTasks / totalTasks) * 100);
  const barFill = document.getElementById('progress-bar-fill');
  const progressText = document.getElementById('progress-text');
  if (barFill) {
    barFill.style.width = percentage + '%';
  }
  if (progressText) {
    progressText.textContent = `Fortschritt: ${completedTasks} von ${totalTasks} Aufgaben erledigt (${percentage}%)`;
  }
}

// Initial update on page load
document.addEventListener('DOMContentLoaded', () => {
  if (typeof updateProgress === 'function') {
    updateProgress();
  }
  initAdminLogo();
});

function initAdminLogo() {
  if (!document.body || document.querySelector('.admin-logo')) {
    return;
  }

  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'admin-logo';
  button.setAttribute('aria-label', 'Verstecktes Portfolio-Logo');
  button.setAttribute('title', 'Portfolio-Logo');
  button.innerHTML = '<span>CP</span>';
  document.body.appendChild(button);

  let clickCount = 0;
  let resetTimer;

  const resetClickCount = () => {
    clickCount = 0;
    if (resetTimer) {
      clearTimeout(resetTimer);
      resetTimer = undefined;
    }
  };

  button.addEventListener('click', () => {
    clickCount += 1;
    button.classList.add('admin-logo-active');
    setTimeout(() => button.classList.remove('admin-logo-active'), 150);

    if (resetTimer) {
      clearTimeout(resetTimer);
    }
    resetTimer = setTimeout(resetClickCount, 2000);

    if (clickCount >= 5) {
      resetClickCount();
      const adminUrl = new URL('admin/index.php', window.location.href);
      window.location.href = adminUrl.toString();
    }
  });
}
