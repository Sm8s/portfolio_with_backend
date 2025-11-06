</main>
<footer class="container my-5 text-center text-secondary small">
  <hr class="border-secondary opacity-50">
  <div>Admin · C‑Portfolio</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// subtle hover parallax for cards
document.querySelectorAll('.card.hover-tilt').forEach(card => {
  card.addEventListener('mousemove', (e)=>{
    const r = card.getBoundingClientRect();
    const x = (e.clientX - r.left)/r.width - 0.5;
    const y = (e.clientY - r.top)/r.height - 0.5;
    card.style.transform = `rotateX(${(-y*4).toFixed(2)}deg) rotateY(${(x*4).toFixed(2)}deg) translateZ(0)`;
  });
  card.addEventListener('mouseleave', ()=> card.style.transform='');
});
</script>
</body>
</html>