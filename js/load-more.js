let page = 2;
let loading = false;
let totalPages = null;

const observer = new IntersectionObserver((entries) => {
  if (entries[0].isIntersecting && !loading && (totalPages === null || page <= totalPages)) {
    loadMorePosts();
  }
}, {
  rootMargin: '300px',
  threshold: 0.1
});

// Make sure the scroll trigger exists before observing
const scrollTrigger = document.getElementById('idScrollTrigger');
if (scrollTrigger) {
  observer.observe(scrollTrigger);
}

function loadMorePosts() {
  if (totalPages !== null && page > totalPages) {
    observer.disconnect();
    return;
  }

  loading = true;

  fetch(`${wpApiSettings.root}wp/v2/posts?page=${page}&per_page=6&_embed=true`, {
    headers: {
      'X-WP-Nonce': wpApiSettings.nonce
    }
  })
    .then((res) => {
      if (!res.ok) {
        throw new Error('No more posts');
      }
      // Get total pages from headers
      totalPages = parseInt(res.headers.get('X-WP-TotalPages'));
      return res.json();
    })
    .then((posts) => {
      const container = document.getElementById('idStories');
      posts.forEach((post) => {
        // Get author name from meta data
        const authorFirstName = post.author_meta.first_name || '';
        const authorLastName = post.author_meta.last_name || '';
        const authorName = [authorFirstName, authorLastName].filter(Boolean).join(' ');

        // Get categories (excluding 'Uncategorized')
        const categories = post._embedded['wp:term'][0]
          .filter(cat => cat.slug !== 'uncategorized')
          .map(cat => cat.name);

        const postDate = new Date(post.date).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });

        const el = document.createElement('div');
        el.className = 'divStory';
        el.innerHTML = `
          <div class="divStoryTitle">
            <a href="${post.link}">${post.title.rendered}</a>
          </div>
          <div class="divLineUnderStoryTitle">
            ${postDate} by ${authorName}
          </div>
          <div class="divStoryBody">
            ${post.content.rendered}
          </div>
          ${categories.length ? `
            <div class="divCategories">
              Categories: ${categories.join(', ')}.
            </div>
          ` : ''}
        `;
        container.appendChild(el);
      });
      page++;
      loading = false; // Reset loading flag
    })
    .catch((error) => {
      console.log('Error loading posts:', error);
      observer.disconnect();
      loading = false;
    });
}
