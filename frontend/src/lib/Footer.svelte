<footer class="footer">
  <div class="container">
    <div class="contact">
      <h4>Contact</h4>
      <p>Sportlaan 123<br />1254 AB Soad<br />012-3455-69</p>
    </div>
    <div class="socials">
      <h4>Social Media</h4>
      <div class="icons">
        <a href="#" aria-label="Facebook">&#xf09a;</a>
        <a href="#" aria-label="Instagram">&#xf16d;</a>
        <a href="#" aria-label="Twitter">&#xf099;</a>
      </div>
    </div>
    <div class="quick-links">
      <h4>Quick Links</h4>
      <a href="#">Sitemap</a><br />
      <a href="#">Privacy</a><br />
      <a href="#">Voleinizer</a>
    </div>
  </div>
</footer>
<style>
.footer { width: 100%; background: var(--primary); color: #fff; padding: 2rem 0; }
.container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; gap: 2rem; }
.footer h4 { margin-bottom: 0.5rem; font-size: 1.1rem; }
.footer a { color: #fff; text-decoration: none; margin-right: 0.5rem; font-size: 1.1rem; }
.footer a:hover { text-decoration: underline; }
.icons { display: flex; gap: 1rem; font-family: 'Font Awesome 5 Free', Arial, sans-serif; font-weight: 900; font-size: 1.5rem; }
@media (max-width: 900px) {
  .container { flex-direction: column; gap: 2rem; align-items: flex-start; }
}
</style> 