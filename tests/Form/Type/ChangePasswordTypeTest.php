<?php

namespace Tests\IPC\SecurityBundle\Form\Type;

use IPC\SecurityBundle\Form\Type\ChangePasswordType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ChangePasswordTypeTest extends TypeTestCase
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $metadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();
        $this->validator->expects($this->any())->method('getMetadataFor')->will($this->returnValue($metadata));
        $this->validator->expects($this->any())->method('validate')->will($this->returnValue(array()));

        parent::setUp();

        /* not required on symfony >= 3.3 */
        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->getFormFactory();
    }

    protected function getExtensions()
    {
        return array_merge(parent::getExtensions(), array(
            new ValidatorExtension($this->validator),
        ));
    }

    protected function getTypeExtensions()
    {
        return [
            new FormTypeValidatorExtension($this->validator),
        ];
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $form     = new ChangePasswordType();
        $form->configureOptions($resolver);

        $this->assertTrue($resolver->hasDefault('data_class'));
        $this->assertTrue($resolver->hasDefault('validation_groups'));
        $this->assertTrue($resolver->isRequired('require_current'));
        $this->assertTrue($resolver->isRequired('require_repeated'));

        $options = $resolver->resolve(['require_current' => false, 'require_repeated' => false]);
        $this->assertTrue(is_callable($options['validation_groups']));
        $this->assertEquals(['Default'], $options['validation_groups']());
    }

    public function testBuildFormNoCurrentNoRepeated()
    {
        $this->assertCount(0, $this->builder);

        $form = new ChangePasswordType();
        $form->buildForm($this->builder, ['require_current' => false, 'require_repeated' => false]);

        $this->assertCount(2, $this->builder);

        $this->assertTrue($this->builder->has('new'));
        $new = $this->builder->get('new');
        $this->assertFalse($new->getOption('required'));
        $this->assertEquals('form.type.change_password.new.label', $new->getOption('label'));

        $this->assertTrue($this->builder->has('update'));
        $update = $this->builder->get('update');
        $this->assertEquals('common.button.change', $update->getOption('label'));

        $reflectionProperty = new \ReflectionProperty(ChangePasswordType::class, 'validationGroups');
        $reflectionProperty->setAccessible(true);
        $this->assertEquals(['Default'], $reflectionProperty->getValue($form));
    }

    public function testBuildFormNoRepeated()
    {
        $this->assertCount(0, $this->builder);

        $form = new ChangePasswordType();
        $form->buildForm($this->builder, ['require_current' => true, 'require_repeated' => false]);
        $this->assertCount(3, $this->builder);
        $this->assertCurrent();
        $this->assertNew();
        $this->assertUpdate();

        $reflectionProperty = new \ReflectionProperty(ChangePasswordType::class, 'validationGroups');
        $reflectionProperty->setAccessible(true);
        $this->assertEquals(['Default', 'require_current'], $reflectionProperty->getValue($form));
    }

    public function testBuildForm()
    {
        $this->assertCount(0, $this->builder);

        $form = new ChangePasswordType();
        $form->buildForm($this->builder, ['require_current' => true, 'require_repeated' => true]);

        $this->assertCount(3, $this->builder);
        $this->assertRepeated();
        $this->assertCurrent();
        $this->assertUpdate();

        $reflectionProperty = new \ReflectionProperty(ChangePasswordType::class, 'validationGroups');
        $reflectionProperty->setAccessible(true);
        $this->assertEquals(['Default', 'require_current', 'require_repeated'], $reflectionProperty->getValue($form));
    }

    protected function assertCurrent()
    {
        $this->assertTrue($this->builder->has('current'));
        $current = $this->builder->get('current');
        $this->assertFalse($current->getOption('required'));
        $this->assertEquals('form.type.change_password.current.label', $current->getOption('label'));
    }

    protected function assertRepeated()
    {
        $this->assertTrue($this->builder->has('new'));
        $repeated = $this->builder->get('new');
        $this->assertEquals(RepeatedType::class, get_class($repeated->getType()->getInnerType()));
        $this->assertFalse($repeated->getOption('required'));
        $this->assertEquals('new', $repeated->getOption('first_name'));
        $this->assertEquals('repeated', $repeated->getOption('second_name'));
        $this->assertEquals(['label' => 'form.type.change_password.new.label'], $repeated->getOption('first_options'));
        $this->assertEquals(['label' => 'form.type.change_password.repeated.label'], $repeated->getOption('second_options'));
        $this->assertEquals('form.type.change_password.new.invalid_message', $repeated->getOption('invalid_message'));
    }

    protected function assertNew()
    {
        $this->assertTrue($this->builder->has('new'));
        $new = $this->builder->get('new');
        $this->assertEquals(PasswordType::class, get_class($new->getType()->getInnerType()));
        $this->assertFalse($new->getOption('required'));
        $this->assertEquals('form.type.change_password.new.label', $new->getOption('label'));
    }

    protected function assertUpdate()
    {
        $this->assertTrue($this->builder->has('update'));
        $update = $this->builder->get('update');
        $this->assertEquals('common.button.change', $update->getOption('label'));
    }
}