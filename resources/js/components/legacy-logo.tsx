export default function LegacyLogo({ compact = false }: { compact?: boolean }) {
    return (
        <div
            className={
                compact ? 'legacy-logo legacy-logo-compact' : 'legacy-logo'
            }
        >
            <div className="legacy-flag">
                <span />
            </div>
            <div className="legacy-logo-text">
                <strong>
                    NE<span>X</span>TGEN
                </strong>
                <em>Technology</em>
            </div>
        </div>
    );
}
