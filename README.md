# 5.2 create index
PUT /library
{
"settings": {
"index": {
"number_of_shards": 1,  
"number_of_replicas": 0
}
}
}

# 5.4 update static setting
PUT /library/_settings
{
"index" : {
"number_of_replicas" : 2
}
}

# 5.4 update dynamic setting
POST /library/_close
PUT /library/_settings
{
"index.shard.check_on_startup": "checksum"
}
POST /library/_open

# 5.5 add document
POST /library/_doc/1
{
"title": "Robin des bois"
}

# 5.5 update document
POST /library/_update/1
{
"doc": {
"title": "Robin des bois 2",
"author": "John Doe"
}
}

# 5.5 suppression d'un document
DELETE /library/_doc/1

# 5.6 bulk
POST /_bulk
{ "index" : { "_index" : "library", "_id" : "1" } }
{ "title" : "Jessie", "author": "Stephen King" }
{ "index" : { "_index" : "library", "_id" : "2" } }
{ "title" : "Harry Potter", "author": "J. K. Rowling" }
{ "index" : { "_index" : "library", "_id" : "3" } }
{ "title" : "Les misérables", "author": "Victor Hugo" }
{ "index" : { "_index" : "library", "_id" : "4" } }
{ "title" : "Le château de ma mère", "author": "Marcel Pagnal" }

POST /library/_bulk
{ "index" : { "_id" : "4" } }
{ "title" : "Au bonheur des dames", "author": "Émile Zola" }
{ "delete" : { "_id" : "2" } }
{ "update" : { "_id" : "4" } }
{ "doc" : { "author": "Marcel Pagnal" } }

# 5.7 simple search
GET /library/_search?q=title:Jessie

# 5.7 SQL search
POST /library/_sql
{
"query": "SELECT * FROM library ORDER BY sold DESC LIMIT 5"
}

# 6.3 create mapping
PUT /library/_mapping
{
"properties": {
"author": {"type": "text"},
"title": {"type": "text"},
"description": {"type": "text"},
"category": {"type": "text"},
"edition": {"type": "keyword"},
"isbn": {"type": "keyword"},
"releaseDate": {"type": "date"},
"price": {"type": "float"},
"sold": {"type": "integer"}
}
}

# 6.4 analyse
GET /_analyze
{
"char_filter" : ["html_strip"],
"text" : "La <strong>Coopérative</strong> des Tilleuls"
}

GET /_analyze
{
"tokenizer" : "standard",
"text" : "La Coopérative des Tilleuls les-tilleuls.coop"
}

GET /_analyze
{
"filter" : ["lowercase"],
"text" : "La Coopérative des Tilleuls"
}

# 6.5 analyser built-in (1)
GET /_analyze
{
"analyzer": "standard",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET /_analyze
{
"analyzer": "simple",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET /_analyze
{
"analyzer": "whitespace",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET /_analyze
{
"analyzer": "lowercase",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

# 6.6 analyser built-in (2)
GET /_analyze
{
"analyzer": "keyword",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET /_analyze
{
"analyzer": "pattern",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET /_analyze
{
"analyzer": "french",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET /_analyze
{
"analyzer": "fingerprint",
"text" : "Yes yes, Gödel said this sentence is consistent and."
}

# 6.7 custom analyser
PUT /library
{
"settings": {
"analysis": {
"analyzer": {
"my_custom_analyzer": {
"type": "custom",
"char_filter": [
"html_strip"
],
"tokenizer": "standard",
"filter": [
"lowercase",
"asciifolding"
]
}
}
}
}
}

POST /library/_analyze
{
"analyzer": "my_custom_analyzer",
"text": "Is this <b>déjà vu</b>?"
}




# get index
GET /library

# get index settings
GET /library/_settings


POST _sql
{
"query": "SELECT * FROM library ORDER BY sold DESC LIMIT 5"
}

# set mapping
PUT /library/_mapping
{
"properties": {
"author": {"type": "text"},
"title": {"type": "text"},
"description": {"type": "text"},
"category": {"type": "text"},
"edition": {"type": "keyword"},
"isbn": {"type": "keyword"},
"releaseDate": {"type": "date"},
"price": {"type": "float"},
"sold": {"type": "integer"}
}
}

# analyse
GET /_analyze
{
"char_filter" : ["html_strip"],
"text" : "La <strong>Coopérative</strong> des Tilleuls"
}
GET /_analyze
{
"tokenizer" : "standard",
"text" : "La Coopérative des Tilleuls les-tilleuls.coop"
}
GET /_analyze
{
"filter" : ["lowercase"],
"text" : "La Coopérative des Tilleuls"
}

GET /_analyze
{
"analyzer" : "english",
"text" : "The 2 QUICK Brown-Foxes jumped over the lazy dog's bone."
}

GET library/_explain/t4HnnngBXKqNLCwETAjp
{
"query": {
"match": {
"author": "martin"
}
}
}

PUT /library/_mapping
{
"properties": {
"author": {
"type": "text",
"analyzer": "french"
},
"title": {
"type": "text",
"analyzer": "french"
},
"description": {
"type": "text",
"analyzer": "french"
},
"category": {
"type": "text",
"analyzer": "french"
},
"edition": {
"type": "keyword"
},
"isbn": {
"type": "keyword"
},
"releaseDate": {
"type": "date"
},
"price": {
"type": "float"
},
"sold": {
"type": "integer"
}
}
}

GET library/_count

GET library/_search
{
"query": {
"match_all": {}
}
}

GET library/_search
{
"query": {
"term": {
"subCategory.keyword": "Mangas"
}
}
}

GET library/_search
{
"size": 0,
"aggs": {
"comics": {
"terms": {
"field": "subCategory.keyword"
, "order": {
"prices": "desc"
}
},
"aggs": {
"prices": {
"avg": {
"field": "price"
}
}
}
}
}
}

POST library/_search
{
"size": 0,
"query": {
"range": {
"releaseDate": {
"gte": "now-1w/d",
"lte": "now/d"
}
}
},
"aggs": {
"number_of_sales_by_day": {
"date_histogram": {
"field": "releaseDate",
"calendar_interval": "day"
},
"aggs": {
"number_of_sales": {
"sum": {
"field": "sold"
}
}
}
},
"max_number_of_sales": {
"max_bucket": {
"buckets_path": "number_of_sales_by_day>number_of_sales"
}
}
}
}
GET library/_mapping
POST library/_search
POST library/_search
{
"suggest" : {
"author-suggestion" : {
"text" : "mart",
"term" : {
"field" : "author"
}
}
}
}

GET library/_search
{
"query": {
"match": { "description": "musique" }
},
"highlight": {
"fields": {
"description": {}
}
}
}

GET _slm/stats
