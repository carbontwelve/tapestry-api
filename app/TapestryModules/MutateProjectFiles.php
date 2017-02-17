<?php

namespace App\TapestryModules;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Entities\Permalink;
use Tapestry\Entities\Project;
use Tapestry\Step;

class MutateProjectFiles implements Step
{
    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        /** @var ContentType $contentType */
        foreach ($project['content_types']->all() as $contentType) {

            foreach (array_keys($contentType->getFileList()) as $fileKey) {
                /** @var File $file */
                if (!$file = $project->get('files.' . $fileKey)) {
                    continue;
                }

                $file->setData([
                    'content_type' => $contentType->getName(),
                    'content_type_permalink' => $contentType->getPermalink()
                ]);

                if ($contentType->getPermalink() !== '*') {
                    $file->setPermalink(new Permalink($contentType->getPermalink()));
                }
            }
        }
        return true;
    }
}