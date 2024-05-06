<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Space\Blog\Model\ResourceModel\Post\CollectionFactory;
use Space\Blog\Model\PostFactory;
use Space\Blog\Model\PostRepository;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;

class Generate extends Command
{
    /**
     * Count constant
     */
    private const COUNT = 'count';

    /**
     * Minimum post count
     */
    private const MIN = 5;

    /**
     * Maximum post count
     */
    private const MAM = 20;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var PostFactory
     */
    private PostFactory $postFactory;

    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param PostFactory $postFactory
     * @param PostRepository $postRepository
     * @param StoreManagerInterface $storeManager
     * @param string|null $name
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        PostFactory $postFactory,
        PostRepository $postRepository,
        StoreManagerInterface $storeManager,
        string $name = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        $this->storeManager = $storeManager;
        parent::__construct($name);
    }

    /**
     * Configure
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('space:blog:generate');
        $this->setDescription('Generate custom posts for blog module');
        $this->addOption(
            self::COUNT,
            null,
            InputOption::VALUE_REQUIRED,
            'Required parameter for posts count to generate from 5 to 20'
        );

        parent::configure();
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = Cli::RETURN_SUCCESS;

        try {
            if ($count = $input->getOption(self::COUNT)) {
                if (empty($count)
                    || !is_numeric($count)
                    || $count < self::MIN
                    || $count > self::MAM
                ) {
                    throw new LocalizedException(__('Parameter count has to be a number between 5 and 20'));
                }

                $collection = $this->collectionFactory->create();
                $size = $collection->getSize();
                if ($size > 0) {
                    throw new LocalizedException(__('Post collection must be empty. Current size: %1', $size));
                }

                $output->writeln('<info>Starting post generation...</info>');
                $storeId = $this->storeManager->getDefaultStoreView()->getId();
                for ($i = 1; $i <= $count; $i++) {
                    $postValues = $this->getCustomBlogValues($i);
                    $post = $this->postFactory->create();
                    $post->setTitle($postValues['title']);
                    $post->setAuthor($postValues['author']);
                    $post->setStoreId($storeId);
                    $post->setContent($postValues['content']);
                    $post->setIsActive(true);
                    $this->postRepository->save($post);

                    if (!$post->getId()) {
                        throw new LocalizedException(__('Unable to create post'));
                    }

                    $output->writeln(sprintf('<info>Post created with ID: %s</info>', $post->getId()));
                }
            } else {
                throw new LocalizedException(__('Please provide count parameter. See space:blog:generate -h'));
            }
        } catch (LocalizedException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            $exitCode = Cli::RETURN_FAILURE;
        }

        return $exitCode;
    }

    /**
     * Get custom blog values
     *
     * @param int $count
     * @return array
     */
    private function getCustomBlogValues(int $count): array
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor ' .
            'incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ' .
            'ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit ' .
            'in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat ' .
            'non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        return [
            'title' => 'Test title ' . $count,
            'author' => $this->getRandomAuthor(),
            'content' => $content
        ];
    }

    /**
     * Get random author
     *
     * @return string
     */
    private function getRandomAuthor(): string
    {
        $key = rand(0, 3);
        $authors = ['James', 'Penny', 'Mario', 'John'];

        return $authors[$key];
    }
}
