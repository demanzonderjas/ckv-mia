<section class="results">
  <h3>Laatste Wedstrijden</h3>
  <table>
    <thead>
      <tr><th>Team</th><th>Tegenstander</th><th>Uitslag</th><th>Datum</th></tr>
    </thead>
    <tbody>
      {#each results as r}
        <tr>
          <td>{r.team}</td>
          <td>{r.opponent}</td>
          <td>{r.result}</td>
          <td>{r.date}</td>
        </tr>
      {/each}
    </tbody>
  </table>
</section>
<script>
  let results = [
    { team: 'MIA 1', opponent: "DOS'46", result: '15 – 16', date: '29 juli 2025' },
    { team: 'MIA A1', opponent: 'Dalto', result: '12 – 12', date: '19 juli 2025' }
  ];
</script>
<style>
.results { padding: 2rem 0; }
.results h3 { color: var(--primary); font-size: 1.5rem; margin-bottom: 1.2rem; }
table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
th, td { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; text-align: left; }
th { background: var(--primary); color: #fff; font-weight: 600; border: none; }
td { font-size: 1.05rem; }
</style> 