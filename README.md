# delete index
DELETE /library

# create index
PUT /library
{
"settings": {
"index": {
"number_of_shards": 1,  
"number_of_replicas": 2
}
}
}

# get index
GET /library


# update index dynamic settings


# update index static settings
POST /library/_close
PUT /library/_settings
{
"index.shard.check_on_startup": "checksum"
}
POST /library/_open

# get index settings
GET /library/_settings

# create document
POST /library/_doc/1
{
"name": "bary"
}

# update document
POST /library/_update/1
{
"doc": {
"foo": "bar1"
}
}

# delete document
DELETE /library/_doc/1

# batch
POST _bulk
{ "index" : { "_index" : "test", "_id" : "1" } }
{ "field1" : "value1" }
{ "delete" : { "_index" : "test", "_id" : "2" } }
{ "create" : { "_index" : "test", "_id" : "3" } }
{ "field1" : "value3" }
{ "update" : {"_id" : "1", "_index" : "test"} }
{ "doc" : {"field2" : "value2"} }

# simple search
GET /library/_search?q=subCategory:Comics

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

GET /_analyze
{
"analyzer" : "standard",
"text" : "La Coopérative des Tilleuls les-tilleuls.coop!"
}
GET /_analyze
{
"char_filter" : ["html_strip"],
"text" : "La <strong>Coopérative</strong> des Tilleuls"
}

GET /_analyze
{
"tokenizer" : "standard",
"text" : "La Coopérative des Tilleuls"
}

