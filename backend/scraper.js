import axios from "axios";
import fs from "fs"
import { load } from "cheerio"

const url = 'https://www.ckvmia.nl/sponsorpakketten';
const selector = '.nxs-article-container';

async function scrape() {
  const { data } = await axios.get(url);
  const $ = load(data);
  const partial = $(selector).html();

  if (!partial) {
    console.error('Selector not found!');
    process.exit(1);
  }


  // Optionally, sanitize or transform the HTML here

  fs.writeFileSync('scraped.html', partial);
  console.log('Scraped!');
}

scrape();