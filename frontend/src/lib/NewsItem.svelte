<script lang="ts">
  export let news: {
    id: number;
    title: string;
    image_url?: string;
    category?: { name: string };
  };
</script>
<a class="news-item" href={news.id ? `/news/${news.id}` : '#'}>
  <img src={news.image_url || '/news1.jpg'} alt={news.title} />
  <div>
    {#if news.category?.name}
      <span class="category-tag"><span class="tag-icon">🏷️</span> {news.category.name.toUpperCase()}</span>
    {/if}
    <h4>{news.title}</h4>
    <span class="read-more">Read more &rarr;</span>
  </div>
</a>
<style>
.news-item {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px #0001;
  width: 240px;
  text-decoration: none;
  color: inherit;
  transition: box-shadow 0.15s, transform 0.15s;
}
.news-item:hover {
  box-shadow: 0 4px 16px #0002;
  transform: translateY(-2px) scale(1.02);
}
.news-item img { width: 100%; height: 110px; object-fit: cover; }
.news-item h4 { margin: 0.5rem 1rem 0.25rem; font-size: 1.05rem; font-weight: 600; }
.read-more {
  margin: 0 1rem 1rem;
  color: var(--primary);
  font-weight: 500;
  font-size: 0.98rem;
  display: block;
}
.category-tag {
  display: inline-flex;
  align-items: center;
  background: var(--primary, #ff6600);
  color: #fff;
  font-size: 0.92rem;
  font-weight: 700;
  border-radius: 999px;
  padding: 0.25rem 1rem 0.25rem 0.7rem;
  margin: 0.7rem 1rem 0 1rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  box-shadow: 0 2px 8px #0002;
  gap: 0.5em;
}
.tag-icon {
  font-size: 1.1em;
  margin-right: 0.3em;
}
@media (max-width: 900px) {
  .news-item { width: 100%; }
}
</style> 