
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
  updateProgress();
});
