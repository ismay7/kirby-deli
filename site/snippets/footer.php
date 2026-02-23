    <footer>
      <div id="footer-left" class="footer-section">
        <p class="copyright">&copy; <?php echo date("Y"); ?> La Rotonda GmbH</p>
        <p class="by">design + code by <a target="_blank" href="https://kultformat.ch">#kultformat</a></p>
        <p><a href="">Impressum + Datenschutz</a></p>
      </div>

      <div id="footer-right" class="footer-section">
        <p class="social-links">
          <a href=""><span class="proicons--instagram"></span></a>
          <a href=""><span class="proicons--facebook"></span></a>
          <a href=""><span class="jam--linkedin-square"></span></a>
        </p>
        <p><a href="">AGB</a></p>
        <p><a href="">Offene Stellen</a></p>
      </div>
    </footer>

    </main>

     <?php if ($page->isHomePage()): ?>
      <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js" defer></script>
    <?php endif ?>
      <!-- Vendor scripts -->
    <script src="https://unpkg.com/aos@next/dist/aos.js" defer></script>

    <?= js([
      'assets/js/app.js',
      ]) ?>

  </body>
</html>