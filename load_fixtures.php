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
const NUMBER_OF_AUTHORS = 10000;
const NUMBER_OF_EDITIONS = 500;
const INDEX_NAME = 'library';
const BULK_SIZE = 1000;

$faker = Faker\Factory::create('fr_FR');

$authors = [];
for ($i = 1; $i <= NUMBER_OF_AUTHORS; $i++) {
    $authors[] = $faker->name();
}

$editions = [];
for ($i = 1; $i <= NUMBER_OF_EDITIONS; $i++) {
    $editions[] = $faker->company();
}

$total = 0;
$documents = [];

while ($total <= BULK_SIZE) {
    $category = \array_rand(CATEGORIES);
    $subCategory = CATEGORIES[$category][\array_rand(CATEGORIES[$category])];

    $documents[] = ['index' => ['_index' => INDEX_NAME]];
    $documents[] = [
        'author' => $authors[\mt_rand(1, NUMBER_OF_AUTHORS)],
        'title' => $faker->name(),
        'description' => $faker->text(),
        'category' => $category,
        'subCategory' => $subCategory,
        'edition' => $authors[\mt_rand(1, NUMBER_OF_EDITIONS)],
        'isbn' => $faker->isbn13(),
        'releaseDate' => $faker->date(),
        'price' => $faker->randomFloat(1, 5, 30),
        'sold' => $faker->randomNumber(5, false),
    ];

    $total++;
}
$elasticaClient = new \Elastica\Client();
$elasticaIndex = $elasticaClient->getIndex(INDEX_NAME);
//$elasticaIndex->delete();
//$elasticaIndex->create();
$elasticaClient->bulk($documents);
