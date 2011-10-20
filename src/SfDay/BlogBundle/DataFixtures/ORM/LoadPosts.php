<?php

namespace SfDay\BlogBundle\DataFixtures\Orm;

use Doctrine\Common\DataFixtures\FixtureInterface;
use SfDay\BlogBundle\Entity\Post;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class LoadPosts extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
  
    public function load($manager)
    {
        $jack = $manager->merge($this->getReference('user-jack'));
        $jill = $manager->merge($this->getReference('user-jill'));
      
        $aclProvider = $this->container->get('security.acl.provider');
        $securityContext = $this->container->get('security.context');
        
        $post1 = new Post();
        $post1->setTitle('Oldest post');
        $post1->setBody('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc quis elit at magna eleifend tincidunt at vel ante. Fusce facilisis luctus luctus. Praesent auctor viverra elit quis viverra. Nam vehicula lobortis eros at iaculis. In hac habitasse platea dictumst. Proin in placerat enim. Proin vehicula nulla quis quam molestie faucibus vitae id odio');
        $post1->setPublishedAt(new \DateTime('yesterday'));
        $post1->setAuthor($jack);
        $manager->persist($post1);
        $manager->flush();
        
        $objectIdentity = Domain\ObjectIdentity::fromDomainObject($post1);
        $acl = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the currently logged-in user
        
        $securityIdentity = new Domain\UserSecurityIdentity($jack->getUsername(), "SfDay\UserBundle\Entity\User");

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);

        $post2 = new Post();
        $post2->setTitle('Newest post');
        $post2->setBody('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc quis elit at magna eleifend tincidunt at vel ante. Fusce facilisis luctus luctus. Praesent auctor viverra elit quis viverra. Nam vehicula lobortis eros at iaculis. In hac habitasse platea dictumst. Proin in placerat enim. Proin vehicula nulla quis quam molestie faucibus vitae id odio');
        $post2->setPublishedAt(new \DateTime('today'));
        $post2->setAuthor($jill);
        $manager->persist($post2);
        $manager->flush();
        
        $objectIdentity = Domain\ObjectIdentity::fromDomainObject($post2);
        $acl = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the currently logged-in user
        $securityIdentity = new Domain\UserSecurityIdentity($jill->getUsername(), "SfDay\UserBundle\Entity\User");

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);
    }
    
    public function getOrder()
    {
        return 2;
    }
}