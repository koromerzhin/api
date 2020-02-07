<?php

namespace Labstag\DataFixtures;

use bheller\ImagesGenerator\ImagesGeneratorProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use finfo;
use Labstag\Entity\Bookmark;
use Labstag\Repository\TagsRepository;
use Labstag\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookmarkFixtures extends Fixture implements DependentFixtureInterface
{
    private const NUMBER = 25;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    public function __construct(UserRepository $userRepository, TagsRepository $tagsRepository)
    {
        $this->userRepository = $userRepository;
        $this->tagsRepository = $tagsRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $this->add($manager);
    }

    public function getDependencies(): array
    {
        return [
            FilesFixtures::class,
            TagsFixtures::class,
            UserFixtures::class,
        ];
    }

    private function add(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();
        $tags  = $this->tagsRepository->findBy(['type' => 'bookmark']);
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new ImagesGeneratorProvider($faker));
        /** @var resource $finfo */
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        for ($index = 0; $index < self::NUMBER; ++$index) {
            $bookmark = new Bookmark();
            $bookmark->setUrl($faker->unique()->url);
            $bookmark->setName($faker->unique()->text(rand(5, 50)));
            /** @var string $content */
            $content = $faker->unique()->paragraphs(4, true);
            $bookmark->setContent($content);
            $user = rand(0, 1);
            if ($user) {
                $tabIndex = array_rand($users);
                $bookmark->setRefuser($users[$tabIndex]);
            }

            $this->addTags($bookmark, $tags);

            try {
                $image   = $faker->imageGenerator(
                    null,
                    1920,
                    1920,
                    'jpg',
                    true,
                    $faker->word,
                    $faker->hexColor,
                    $faker->hexColor
                );
                $content = file_get_contents($image);
                /** @var resource $tmpfile */
                $tmpfile = tmpfile();
                $data    = stream_get_meta_data($tmpfile);
                file_put_contents($data['uri'], $content);
                $file = new UploadedFile(
                    $data['uri'],
                    'image.jpg',
                    (string) finfo_file($finfo, $data['uri']),
                    null,
                    true
                );

                $bookmark->setImageFile($file);
            } catch (Exception $exception) {
                echo $exception->getMessage();
            }

            $manager->persist($bookmark);
        }

        $manager->flush();
    }

    private function addTags(Bookmark $bookmark, array $tags): void
    {
        $nbr = rand(0, count($tags));
        if (0 == $nbr) {
            return;
        }

        $tabIndex = array_rand(
            $tags,
            $nbr
        );
        if (is_array($tabIndex)) {
            foreach ($tabIndex as $indendexndex) {
                $bookmark->addTag($tags[$indendexndex]);
            }

            return;
        }

        $bookmark->addTag($tags[$tabIndex]);
    }
}
