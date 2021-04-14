<?php

require_once 'vendor/autoload.php';

const CATEGORIES = [
    'Littérature' => [
        'Romans',
        'Policiers et thrillers',
        'Romances',
        'Science-fiction et Fantasy',
        'Théâtre et poésie',
        'Lettres supérieures',
        'Récits de voyages',
        'Pléiades',
        'Livre audio',
    ],
    'Livres jeunesse' => [
        '0-3 ans',
        '3-6 ans',
        '6-9 ans',
        '9-12 ans',
        'Romans ados',
        'Jeux et loisirs créatifs',
        'Documentaires',
        'Lire en anglais',
        'Livres CD',
    ],
    'BD, Manga et Humour' => [
        'BD Adultes',
        'BD jeunesse',
        'Romans graphiques et BD indépendantes',
        'Mangas',
        'Comics',
        'Humour',
    ],
    'Livres scolaires' => [
        'Maternelle et éveil',
        'Primaire',
        'Collège',
        'Lycée filières générales',
        'Lycée filières professionnelles',
        'BTS',
        'Orientation et métiers',
        'Concours et Classes Prépas',
        'Pédagogie',
        'Dictionnaires et encyclopédies',
        'Cahiers de vacances',
    ],
    'Langues et livres en VO' => [
        'Français langue étrangère',
        'Anglais',
        'Allemand',
        'Espagnol',
        'Portugais',
        'Italien',
        'Arabe',
        'Chinois',
        'Japonais',
        'Russe',
        'Autres langues',
    ],
    'Arts, culture et société' => [
        'Cinéma',
        'Photo',
        'Danse et cirque',
        'Musique',
        'Architecture et urbanisme',
        'Peinture et sculpture',
        'Mode',
        'Histoire de l\'art',
        'Actualités médias et société',
        'Politique',
    ],
    'Tourisme, Voyages et Guides' => [
        'France',
        'Europe',
        'Amérique',
        'Asie',
        'Afrique',
        'Océanie et Australie',
        'Proche et Moyen-Orient',
        'Cartes, Atlas et Plans',
        'Beaux livres et récits de voyages',
    ],
    'Vie pratique' => [
        'Santé et Bien-être',
        'Esotérisme',
        'Développement personnel',
        'Cuisine et Vins',
        'Loisirs créatifs et jeux',
        'Bricolage et jardinage',
        'Informatique',
        'Bâtiment',
        'Calendriers et agendas',
    ],
    'Nature et sports' => [
        'Nature et Animaux',
        'Sports',
        'Mer',
        'Montagne et Randonnée',
        'Ecologie et développement durable',
    ],
    'Sciences humaines et sociales' => [
        'Histoire',
        'Géographie',
        'Droit',
        'Economie et finance',
        'Entreprise et management',
        'Philosophie',
        'Sociologie et ethnologie',
        'Religions',
    ],
    'Sciences et médecine' => [
        'Médecine et paramédical',
        'Psychologie et psychanalyse',
        'Physique, Chimie et Biologie',
        'Mathématiques',
        'Histoire et Philosophie des sciences',
    ],
    'Livres à prix réduits' => [
        'Cuisine et vins',
        'Nature et tourisme',
        'Loisirs et sports',
        'Bien-être et santé',
        'Jeunesse',
        'Arts',
        'BD et humour',
        'Savoirs',
        'Littérature',
        'Toute l\'offre',
    ],
];

const NUMBER_OF_AUTHORS = 1000;
const NUMBER_OF_EDITIONS = 50;
const INDEX_NAME = 'library';
const TOTAL = 5000;
const BULK_SIZE = 100;

$faker = Faker\Factory::create('fr_FR');

$authors = [];
$genders = ['male', 'female'];
for ($i = 1; $i <= NUMBER_OF_AUTHORS; $i++) {
    $gender = $genders[\array_rand($genders)];
    $authors[$i] = [
        'gender' => $gender,
        'title' => $faker->title($gender),
        'fullname' => $faker->name($gender),
        'email' => $faker->email(),
    ];
}

$editions = [];
for ($i = 1; $i <= NUMBER_OF_EDITIONS; $i++) {
    $editions[$i] = [
        'name' => $faker->company(),
        'location' => [
            'lat' => $faker->latitude(43, 50),
            'lon' => $faker->longitude(-1, 3),
        ],
    ];
}

$elasticaClient = new \Elastica\Client();

$elasticaIndex = $elasticaClient->getIndex(INDEX_NAME);

if ($elasticaIndex->exists()) {
    $elasticaIndex->delete();
}

$elasticaIndex->create([
    'settings' => [
        'number_of_shards' => 1,
        'number_of_replicas' => 0,
    ],
    'mappings' => [
        'dynamic' => 'strict',
        'properties' => [
            'author' => [
                'type' => 'object',
                'properties' => [
                    'title' => ['type' => 'keyword'],
                    'fullname' => ['type' => 'text', 'analyzer' => 'french',  'fields' => ['keyword' => ['type' => 'keyword']]],
                    'email' => ['type' => 'keyword'],
                ]
            ],
            'edition' => [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'text', 'analyzer' => 'french',  'fields' => ['keyword' => ['type' => 'keyword']]],
                    'location' => ['type' => 'geo_point'],
                ]
            ],
            'isbn' => ['type' => 'keyword'],
            'title' => ['type' => 'text', 'analyzer' => 'french',  'fields' => ['keyword' => ['type' => 'keyword']]],
            'description' => ['type' => 'text', 'analyzer' => 'french'],
            'category' => ['type' => 'text', 'analyzer' => 'french',  'fields' => ['keyword' => ['type' => 'keyword']]],
            'subCategory' => ['type' => 'text', 'analyzer' => 'french',  'fields' => ['keyword' => ['type' => 'keyword']]],
            'releaseDate' => ['type' => 'date'],
            'price' => ['type' => 'float'],
            'sales' => ['type' => 'integer'],
            'ratings' => [
                'type' => 'nested',
                'properties' => [
                    'rating' => ['type' => 'integer'],
                    'username' => ['type' => 'keyword'],
                ]
            ]
        ]
    ]
]);

$total = 0;
while ($total < TOTAL) {
    $documents = [];

    while (\count($documents) < (BULK_SIZE * 2)) {
        $category = \array_rand(CATEGORIES);
        $subCategory = CATEGORIES[$category][\array_rand(CATEGORIES[$category])];

        $author = $authors[\random_int(1, NUMBER_OF_AUTHORS)];
        $edition = $editions[\random_int(1, NUMBER_OF_EDITIONS)];

        $documents[] = ['index' => ['_index' => INDEX_NAME]];
        $document = [
            'author' => [
                'title' => $author['title'],
                'fullname' => $author['fullname'],
                'email' => $author['email'],
            ],
            'edition' => [
                'name' => $edition['name'],
                'location' => $edition['location'],
            ],
            'isbn' => $faker->isbn13(),
            'title' => $faker->realTextBetween(3, 25),
            'description' => $faker->realText(),
            'category' => $category,
            'subCategory' => $subCategory,
            'releaseDate' => $faker->dateTimeBetween(new \DateTime('-30 years'), new \DateTime())->format('Y-m-d'),
            'price' => $faker->randomFloat(1, 5, 30),
            'sales' => $faker->randomNumber(4, false),
        ];

        $totalRatings = \random_int(0, 5);
        for ($i = 0; $i <= $totalRatings; $i++) {
            $document['ratings'][] = [
                'rating' => random_int(0, 5),
                'username' => $faker->userName(),
            ];
        }

        $documents[] = $document;
    }

    $elasticaClient->bulk($documents);
    $total += \count($documents) / 2;
    var_dump($total);
}
