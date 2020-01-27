import scrapy
import json

filename = "book.txt"  # To save store data

class IntroSpider(scrapy.Spider):
    name = "book_spider"     # Name of the scraper

    def start_requests(self):
        urls = [
            'http://books.toscrape.com/catalogue/page-{x}.html'.format(x=x) for x in range(1, 50)   # x denotes page number
        ]

        for url in urls:
            yield scrapy.Request(url=url, callback=self.parse)
    
    def parse(self, response):
        list_data=[]

        book_list = response.css('article.product_pod > h3 > a::attr(title)').extract()  # accessing the titles
        link_list = response.css('article.product_pod > h3 > a::attr(href)').extract()  # accessing the titles
        price_list = response.css('article.product_pod > div.product_price > p.price_color::text').extract()
        image_link = response.css('article.product_pod > div.image_container > a > img::attr(src)').extract()  # accessing the titles

        i=0;
        for book_title in book_list:
            data={
                'book_title' : book_title,
                'price' : price_list[i],
                'image-url' : image_link[i],
                'url' : link_list[i]
            }
            i+=1
            list_data.append(data)
            
        with open(filename, 'a+') as f:   # Writing data in the file
            for data in list_data : 
                app_json = json.dumps(data)
                f.write(app_json+"\n")