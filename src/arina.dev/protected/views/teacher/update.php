<?php
/**
 * @var $model Teacher
 * @var $this TeacherController
 */
$this->breadcrumbs = array(
    Yii::t('base', 'Teachers') => array('index'),
    $model->getFullName() => array('view', 'id' => $model->id),
    Yii::t('base', 'Updating'),
);

$this->menu = array(
    array('label' => Yii::t('teacher', 'Teacher list'), 'url' => array('index'), 'type' => Booster::TYPE_PRIMARY),
    array('label' => Yii::t('teacher', 'Add new teacher'), 'url' => array('create'), 'type' => Booster::TYPE_PRIMARY),
    array(
        'label' => Yii::t('teacher', 'Delete teacher'),
        'icon' => 'trash',
        'htmlOptions' => array(
            'submit' => array(
                'delete',
                'id' => $model->id,
            ),
            'confirm' => Yii::t('base', 'Do you want to delete this item?'),
        ),
    ),
);
?>

    <h2><?php echo Yii::t('teacher', 'Updating teacher') . " {$model->getFullName()}"; ?></h2>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>