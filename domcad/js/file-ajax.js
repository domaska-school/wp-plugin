/**
 * 
 * Плагин для сайта Структурное подразделение детский сад ГБОУ СОШ с. Домашка
 * Version:     1.3.1
 * Author:      ProjectSoft <projectsoft2009@yandex.ru>
 * Last Update: 2026-07-02
 * 
 */
const suppressViewTransitionRejections = ( viewTransition ) => {
	const noop = () => {};
	viewTransition.ready.catch( noop );
	viewTransition.finished.catch( noop );
};
window.addEventListener("pagereveal", (event) => {
	if ( event.viewTransition ) {
		const transitionType = 'default'; // Only 'default' is supported so far, but more to be added.
		suppressViewTransitionRejections( event.viewTransition );
		event.viewTransition.types.add( transitionType );
	}
});
